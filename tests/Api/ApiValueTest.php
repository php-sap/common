<?php

namespace tests\phpsap\classes\Api;

use phpsap\classes\Api\Table;
use phpsap\exceptions\InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Exception;
use stdClass;
use phpsap\classes\Util\JsonSerializable;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IValue;
use phpsap\classes\Api\Element;
use phpsap\classes\Api\Value;

/**
 * Class tests\phpsap\classes\Api\ApiValueTest
 * @package tests\phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class ApiValueTest extends TestCase
{
    /**
     * Test the constructor and the inherited classes and interfaces.
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testConstructorAndInheritedClasses()
    {
        $value = new Value(Value::TYPE_STRING, 'C1HZDtVZ', Value::DIRECTION_INPUT, false);
        //heritage
        static::assertInstanceOf(JsonSerializable::class, $value);
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
     * @dataProvider \tests\phpsap\classes\Api\ApiElementTest::provideNonStrings
     */
    public function testNonStringDirections($direction)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API value direction to be string!');
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
            [Table::DIRECTION_TABLE]
        ];
    }

    /**
     * Test invalid direction strings.
     * @param string $direction
     * @dataProvider provideInvalidValueDirections
     */
    public function testInvalidValueDirections($direction)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API value direction to be in:');
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
            [new stdClass()]
        ];
    }

    /**
     * Test non-boolean values for the isOptional flag.
     * @param mixed $isOptional
     * @dataProvider provideNonBooleans
     */
    public function testNonBooleanIsOptionalFlags($isOptional)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API value isOptional flag to be boolean!');
        new Value(Value::TYPE_STRING, 'C1HZDtVZ', Value::DIRECTION_INPUT, $isOptional);
    }

    /**
     * Test JSON decode.
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testJsonDecode()
    {
        $element = Value::jsonDecode('{"type":"int","name":"JCmy98c0","direction":"input","optional":false}');
        static::assertInstanceOf(Value::class, $element);
        static::assertSame(IElement::TYPE_INTEGER, $element->getType());
        static::assertSame('JCmy98c0', $element->getName());
        static::assertSame(IValue::DIRECTION_INPUT, $element->getDirection());
        static::assertFalse($element->isOptional());
    }

    /**
     * Test JSON decoding on invalid parameters.
     * @param mixed $json
     * @dataProvider \tests\phpsap\classes\Api\ApiElementTest::provideInvalidJson()
     */
    public function testInvalidJson($json)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON! Expected JSON encoded phpsap\classes\Api\Value string!');
        Value::jsonDecode($json);
    }

    /**
     * Test JSON decoding on invalid parameters.
     * @param mixed $json
     * @dataProvider \tests\phpsap\classes\Api\ApiElementTest::provideInvalidJsonString()
     */
    public function testInvalidJsonString($json)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON! Expected JSON encoded phpsap\classes\Api\Value string!');
        Value::jsonDecode($json);
    }

    /**
     * Data provider for incomplete JSON objects.
     * @return array
     */
    public static function provideIncompleteJsonObjects()
    {
        $return = ApiElementTest::provideIncompleteJsonObjects();
        $return[] = ['{"type":"int","name":"TRD2cpKy"}'];
        $return[] = ['{"type":true,"name":"H5vNFNkl","optional":true}'];
        $return[] = ['{"type":"int","name":711,"direction":"output"}'];
        $obj = new stdClass();
        $obj->type = Value::TYPE_BOOLEAN;
        $obj->name = '9vQWkdZF';
        return $return;
    }

    /**
     * Test JSON decoding on incomplete JSON objects.
     * @param string $json
     * @dataProvider provideIncompleteJsonObjects
     */
    public function testIncompleteJson($json)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON: phpsap\classes\Api\Value is missing');
        Value::jsonDecode($json);
    }

    /**
     * Test fromArray() using non-array input.
     * @param mixed $input
     * @dataProvider \tests\phpsap\classes\Api\ApiElementTest::provideNonArray
     */
    public function testNonArrayFromArray($input)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected array, but got');
        Value::fromArray($input);
    }
}
