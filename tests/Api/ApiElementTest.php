<?php

namespace tests\phpsap\classes\Api;

use phpsap\interfaces\Api\IStruct;
use phpsap\interfaces\Api\ITable;
use PHPUnit_Framework_TestCase;
use stdClass;
use phpsap\classes\Util\JsonSerializable;
use phpsap\interfaces\Api\IElement;
use phpsap\classes\Api\Element;

/**
 * Class tests\phpsap\classes\Api\ApiElementTest
 * @package tests\phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class ApiElementTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the constructor and the inherited classes and interfaces.
     * @throws \PHPUnit_Framework_Exception
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function testConstructorAndInheritedClasses()
    {
        $element = new Element(Element::TYPE_STRING, 'pG545XSy');
        static::assertInstanceOf(JsonSerializable::class, $element);
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
            [new stdClass()]
        ];
    }

    /**
     * Test non-string parameters for element types.
     * @param mixed $type
     * @dataProvider provideNonStrings
     * @expectedException \phpsap\exceptions\InvalidArgumentException
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
            [ITable::TYPE_TABLE],
            [IStruct::TYPE_STRUCT],
            ['']
        ];
    }

    /**
     * Test exception thrown on invalid element types.
     * @param string $type
     * @dataProvider provideInvalidElementTypes
     * @expectedException \phpsap\exceptions\InvalidArgumentException
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
     * @throws \phpsap\exceptions\InvalidArgumentException
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
     * @expectedException \phpsap\exceptions\InvalidArgumentException
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
            [Element::TYPE_STRING, 21, '21'],
            [Element::TYPE_HEXBIN, '534150', 'SAP']
        ];
    }

    /**
     * Test typecasting some data.
     * @param string               $type
     * @param string|int           $value
     * @param bool|int|float|strin $expected
     * @dataProvider provideTypecastData
     * @throws \phpsap\exceptions\InvalidArgumentException
     * @throws \Exception
     */
    public function testTypecast($type, $value, $expected)
    {
        $element = new Element($type, 'wGSkGY6F');
        $actual = $element->cast($value);
        static::assertSame($expected, $actual);
    }

    /**
     * Test DateTime typed elements.
     * @throws \Exception
     */
    public function testDateTimeElements()
    {
        $dateElement = new Element(Element::TYPE_DATE, 'GONyW3vz');
        $actual = $dateElement->cast('20191030');
        static::assertSame('2019-10-30', $actual->format('Y-m-d'));

        $dateElement = new Element(Element::TYPE_DATE, 'Cf8FwZZe');
        $actual = $dateElement->cast('00000000');
        static::assertNull($actual);

        $timeElement = new Element(Element::TYPE_TIME, 'Ma0NRVdj');
        $actual = $timeElement->cast('102030');
        static::assertSame('10:20:30', $actual->format('%H:%I:%S'));

        $timestampElement = new Element(Element::TYPE_TIMESTAMP, '2SNTkpDJ');
        $actual = $timestampElement->cast('20191030102030');
        static::assertSame('2019-10-30 10:20:30', $actual->format('Y-m-d H:i:s'));

        $weekElement = new Element(Element::TYPE_WEEK, '5aWCnRfD');
        $actual = $weekElement->cast('201944');
        static::assertSame('2019W44', $actual->format('o\WW'));
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

    /**
     * Test JSON decode.
     * @throws \PHPUnit_Framework_Exception
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function testJsonDecode()
    {
        $element = Element::jsonDecode('{"type":"string","name":"VaJPAA7d"}');
        static::assertInstanceOf(Element::class, $element);
        static::assertSame(IElement::TYPE_STRING, $element->getType());
        static::assertSame('VaJPAA7d', $element->getName());
    }

    /**
     * Data provider for values, that won't JSON decode to the expected configuration
     * array.
     * @return array
     */
    public static function provideInvalidJson()
    {
        $cfg = new stdClass();
        $cfg->name = 'MqUyFBxx';
        $cfg->type = 'string';
        return [
            [735],
            [5.9],
            [true],
            [false],
            [null],
            [['name' => 'skyhCVIE', 'type' => 'string']],
            [new stdClass()],
            [$cfg]
        ];
    }

    /**
     * Test JSON decoding on invalid parameters.
     * @param mixed $json
     * @dataProvider provideInvalidJson
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON! Expected JSON encoded phpsap\classes\Api\Element string!
     */
    public function testInvalidJson($json)
    {
        Element::jsonDecode($json);
    }

    /**
     * Data provider for values, that won't JSON decode to the expected configuration
     * array.
     * @return array
     */
    public static function provideInvalidJsonString()
    {
        return [
            [''],
            ['{'],
            [']'],
            ['71.74'],
            ['806'],
            ['"type":"int","name":"WjZpErxz"']
        ];
    }

    /**
     * Test JSON decoding on invalid parameters.
     * @param mixed $json
     * @dataProvider provideInvalidJsonString
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON! Expected JSON encoded phpsap\classes\Api\Element string!
     */
    public function testInvalidJsonString($json)
    {
        Element::jsonDecode($json);
    }

    /**
     * Data provider for incomplete JSON objects.
     * @return array
     */
    public static function provideIncompleteJsonObjects()
    {
        return [
            ['{"name":"I2g8g23n"}'],
            ['{"type":930}'],
            ['{"3cQYx9fv":"int"}'],
            ['{}'],
            ['{"name":"skyhCVIE","ymECDAE6":50.4}'],
            ['{"type":"string","YpymmcwI":"v4mm2pb6"}']
        ];
    }

    /**
     * Test JSON decoding on incomplete JSON objects.
     * @param string $json
     * @dataProvider provideIncompleteJsonObjects
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON: phpsap\classes\Api\Element is missing
     */
    public function testIncompleteJsonObjects($json)
    {
        Element::jsonDecode($json);
    }

    /**
     * Data provider for non-array type values.
     * @return array
     */
    public static function provideNonArray()
    {
        return [
            ['dNtKMbKSJ8'],
            [''],
            [89492],
            [83.5],
            [true],
            [false],
            ['[]'],
            [new stdClass()]
        ];
    }

    /**
     * Test fromArray() using non-array input.
     * @param mixed $input
     * @dataProvider provideNonArray
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Expected array, but got
     */
    public function testNonArrayFromArray($input)
    {
        Element::fromArray($input);
    }
}
