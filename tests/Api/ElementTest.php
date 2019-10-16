<?php

namespace tests\phpsap\classes\Api;

use phpsap\classes\Api\Element;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IElement;

/**
 * Class tests\phpsap\classes\Api\ElementTest
 * @package tests\phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class ElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the constructor and the inherited classes and interfaces.
     */
    public function testConstructorAndInheritedClasses()
    {
        $element = new Element(Element::TYPE_STRING, 'pG545XSy');
        static::assertInstanceOf(\JsonSerializable::class, $element);
        static::assertInstanceOf(IElement::class, $element);
        static::assertInstanceOf(Element::class, $element);
        static::assertSame(IElement::TYPE_STRING, $element->getType());
        static::assertSame('pG545XSy', $element->getName());
    }

    /**
     * Data provider for non-string parameters.
     * @return array
     */
    public static function provideNonStrings()
    {
        return [
            [0],
            [1],
            [1.5],
            [true],
            [false],
            [null],
            [[Element::TYPE_STRING]],
            [new \stdClass()]
        ];
    }

    /**
     * Test non-string parameters for element types.
     * @param mixed $type
     * @dataProvider provideNonStrings
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected API element type to be string!
     */
    public function testNonStringTypes($type)
    {
        new Element($type, 'FRWU81mQ');
    }

    /**
     * Data provider for invalid element types.
     * @return array
     */
    public static function provideInvalidElementTypes()
    {
        return [
            ['STRING'],
            ['FlOaT'],
            ['INT'],
            ['integer'],
            ['b00l'],
            ['boolean'],
            ['double'],
            ['long'],
            [IArray::TYPE_ARRAY],
            ['']
        ];
    }

    /**
     * Test exception thrown on invalid element types.
     * @param string $type
     * @dataProvider provideInvalidElementTypes
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected API element type to be in:
     */
    public function testInvalidElementTypes($type)
    {
        new Element($type, '9dnjz4WD');
    }

    /**
     * Data provider for valid element types.
     * @return array
     */
    public static function provideValidElementTypes()
    {
        return [
            [IElement::TYPE_BOOLEAN],
            [IElement::TYPE_INTEGER],
            [IElement::TYPE_FLOAT],
            [IElement::TYPE_STRING]
        ];
    }

    /**
     * Test valid element types.
     * @param string $type
     * @dataProvider provideValidElementTypes
     */
    public function testValidElementTypes($type)
    {
        $element = new Element($type, 'D6vT5LVY');
        static::assertSame($type, $element->getType());
    }

    /**
     * Data provider for invalid element names.
     * @return array
     */
    public static function provideInvalidElementNames()
    {
        $return = static::provideNonStrings();
        $return[] = [''];
        return $return;
    }

    /**
     * Test invalid element names.
     * @param mixed $name
     * @dataProvider provideInvalidElementNames
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected API element name to be string!
     */
    public function testInvalidElementNames($name)
    {
        new Element(Element::TYPE_STRING, $name);
    }

    /**
     * Data provider for some typecast data.
     * @return array
     */
    public static function provideTypecastData()
    {
        return [
            [Element::TYPE_BOOLEAN, '1', true],
            [Element::TYPE_BOOLEAN, '0', false],
            [Element::TYPE_INTEGER, '98', 98],
            [Element::TYPE_FLOAT, '5.7', 5.7],
            [Element::TYPE_STRING, 21, '21']
        ];
    }

    /**
     * Test typecasting some data.
     * @param string $type
     * @param string|int $value
     * @param bool|int|float|strin $expected
     * @dataProvider provideTypecastData
     */
    public function testTypecast($type, $value, $expected)
    {
        $element = new Element($type, 'wGSkGY6F');
        $actual = $element->cast($value);
        static::assertSame($expected, $actual);
    }

    /**
     * Test JSON serializing an element class.
     */
    public function testJsonSerialize()
    {
        $element = new Element(Element::TYPE_INTEGER, 'fcotBFjX');
        $actualJson = json_encode($element);
        $expectedJson = '{"type":"int","name":"fcotBFjX"}';
        static::assertSame($expectedJson, $actualJson);
    }
}
