<?php

namespace tests\phpsap\classes\Api;

use phpsap\classes\Api\Element;
use phpsap\classes\Api\Value;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IValue;

/**
 * Class tests\phpsap\classes\Api\ValueTest
 * @package tests\phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the constructor and the inherited classes and interfaces.
     */
    public function testConstructorAndInheritedClasses()
    {
        $value = new Value(Value::TYPE_STRING, 'C1HZDtVZ', Value::DIRECTION_INPUT, false);
        //heritage
        static::assertInstanceOf(\JsonSerializable::class, $value);
        static::assertInstanceOf(IValue::class, $value);
        static::assertInstanceOf(Value::class, $value);
        static::assertInstanceOf(IElement::class, $value);
        static::assertInstanceOf(Element::class, $value);
        //basic in-out assertion
        static::assertSame(Value::DIRECTION_INPUT, $value->getDirection());
        static::assertFalse($value->isOptional());
    }

    /**
     * Test non-string directions.
     * @param mixed $direction
     * @dataProvider \tests\phpsap\classes\Api\ElementTest::provideNonStrings
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected API value direction to be string!
     */
    public function testNonStringDirections($direction)
    {
        new Value(Value::TYPE_STRING, 'X6Gg7meM', $direction, false);
    }

    /**
     * Data provider for invalid direction strings.
     * @return array
     */
    public static function provideInvalidValueDirections()
    {
        return [
            [''],
            ['INPUT'],
            ['OuTpUt'],
            ['Table'],
            ['in'],
            ['out'],
            [IArray::DIRECTION_TABLE]
        ];
    }

    /**
     * Test invalid direction strings.
     * @param string $direction
     * @dataProvider provideInvalidValueDirections
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected API value direction to be in:
     */
    public function testInvalidValueDirections($direction)
    {
        new Value(Value::TYPE_STRING, 'X6Gg7meM', $direction, false);
    }

    /**
     * Data provider for non-boolean values.
     * @return array
     */
    public static function provideNonBooleans()
    {
        return [
            ['true'],
            ['false'],
            ['0'],
            ['1'],
            [0],
            [1],
            [5.15],
            ['6QNgkt3G'],
            [null],
            [[true]],
            [new \stdClass()]
        ];
    }

    /**
     * Test non-boolean values for the isOptional flag.
     * @param mixed $isOptional
     * @dataProvider provideNonBooleans
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected API value isOptional flag to be boolean!
     */
    public function testNonBooleanIsOptionalFlags($isOptional)
    {
        new Value(Value::TYPE_STRING, 'C1HZDtVZ', Value::DIRECTION_INPUT, $isOptional);
    }
}
