<?php

namespace tests\phpsap\classes\Api;

use PHPUnit_Framework_TestCase;
use stdClass;
use phpsap\classes\Util\JsonSerializable;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IValue;
use phpsap\classes\Api\Element;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Value;

/**
 * Class tests\phpsap\classes\Api\StructTest
 * @package tests\phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class StructTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test the constructor and the inherited classes and interfaces.
     * @throws \PHPUnit_Framework_AssertionFailedError
     * @throws \PHPUnit_Framework_Exception
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function testConstructorAndInheritance()
    {
        $struct = new Struct('9rlCO8It', Struct::DIRECTION_INPUT, false, [
            new Element(Element::TYPE_STRING, 'K82B6qoL')
        ]);
        //heritage
        static::assertInstanceOf(JsonSerializable::class, $struct);
        static::assertInstanceOf(IArray::class, $struct);
        static::assertInstanceOf(Struct::class, $struct);
        static::assertInstanceOf(IValue::class, $struct);
        static::assertInstanceOf(Value::class, $struct);
        static::assertInstanceOf(IElement::class, $struct);
        static::assertInstanceOf(Element::class, $struct);
        //basic in-out
        static::assertSame('9rlCO8It', $struct->getName());
        static::assertSame(IValue::DIRECTION_INPUT, $struct->getDirection());
        static::assertFalse($struct->isOptional());
        static::assertSame(IArray::TYPE_ARRAY, $struct->getType());
        //test members
        $members = $struct->getMembers();
        static::assertInternalType('array', $members);
        static::assertCount(1, $members);
        foreach ($members as $member) {
            /**
             * @var Element $member
             */
            static::assertInstanceOf(Element::class, $member);
            static::assertSame(IElement::TYPE_STRING, $member->getType());
            static::assertSame('K82B6qoL', $member->getName());
        }
    }

    /**
     * Data provider for non-arrays.
     * @return array
     */
    public static function provideNonArrays()
    {
        return [
            ['array'],
            [1],
            [0],
            [448],
            [67.2],
            [true],
            [false],
            [null],
            [new stdClass()]
        ];
    }

    /**
     * Test non-array members.
     * @param mixed $members
     * @dataProvider provideNonArrays
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Expected API struct members to be in an array!
     */
    public function testNonArrayMembers($members)
    {
        new Struct('9rlCO8It', Struct::DIRECTION_INPUT, false, $members);
    }

    /**
     * Test typecasting of struct input
     * @throws \phpsap\exceptions\InvalidArgumentException
     * @throws \phpsap\exceptions\ArrayElementMissingException
     */
    public function testStructCast()
    {
        $struct = new Struct('HVfgbemt', Struct::DIRECTION_INPUT, false, [
            new Element(Element::TYPE_BOOLEAN, 'UmYDghzf'),
            new Element(Element::TYPE_INTEGER, 'xH9lLz9D'),
            new Element(Element::TYPE_FLOAT, 'p6ZMXkFG'),
            new Element(Element::TYPE_STRING, '1Kdi4MOO')
        ]);
        $raw = [
            'UmYDghzf' => '0',
            'xH9lLz9D' => '351',
            'p6ZMXkFG' => '2.8',
            '1Kdi4MOO' => 987257,
            'hsRhK80B' => 'cGOBb1eb' //This element is not defined in the API ...
        ];
        $expected = [
            'UmYDghzf' => false,
            'xH9lLz9D' => 351,
            'p6ZMXkFG' => 2.8,
            '1Kdi4MOO' => '987257',
            'hsRhK80B' => 'cGOBb1eb' //... therefore it stays untouched.
        ];
        $actual = $struct->cast($raw);
        static::assertSame($expected, $actual);
    }

    /**
     * Test casting raw data with an element missing.
     * @expectedException \phpsap\exceptions\ArrayElementMissingException
     * @expectedExceptionMessage Element pUDY31My in struct 1M9enD5H is missing!
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function testStructCastMissingElement()
    {
        $struct = new Struct('1M9enD5H', Struct::DIRECTION_OUTPUT, true, [
            new Element(Element::TYPE_STRING, 'pUDY31My')
        ]);
        $raw = [
            'XCoewjY4' => '76',
            'z7bq9TYE' => '6.84',
            'aPxweADp' => 'X34kVegj'
        ];
        $struct->cast($raw);
    }

    /**
     * Test adding non-IElement members.
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Expected API struct members to be instances of IElement!
     */
    public function testNonIElementMembers()
    {
        new Struct('1M9enD5H', Struct::DIRECTION_OUTPUT, true, [new stdClass()]);
    }

    /**
     * Test JSON decode.
     * @throws \PHPUnit_Framework_Exception
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function testJsonDecode()
    {
        $json = '{"type":"array","name":"l9M7gn6p","direction":"input",'
                .'"optional":true,"members":[{"type":"int","name":"lnrxpRjh"}]}';
        $element = Struct::jsonDecode($json);
        static::assertInstanceOf(Struct::class, $element);
        static::assertSame(IArray::TYPE_ARRAY, $element->getType());
        static::assertSame('l9M7gn6p', $element->getName());
        static::assertSame(IArray::DIRECTION_INPUT, $element->getDirection());
        static::assertTrue($element->isOptional());
        static::assertInternalType('array', $element->getMembers());
        $members = $element->getMembers();
        foreach ($members as $member) {
            /**
             * @var \phpsap\interfaces\Api\IElement $member
             */
            static::assertSame(IArray::TYPE_INTEGER, $member->getType());
            static::assertSame('lnrxpRjh', $member->getName());
        }
    }

    /**
     * Test JSON decoding on invalid parameters.
     * @param mixed $json
     * @dataProvider \tests\phpsap\classes\Api\ApiElementTest::provideInvalidJson()
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON!
     */
    public function testInvalidJson($json)
    {
        Struct::jsonDecode($json);
    }

    /**
     * Data provider for incomplete JSON objects.
     * @return array
     */
    public static function provideIncompleteJsonObjects()
    {
        $return = ApiValueTest::provideIncompleteJsonObjects();
        $return[] = ['{"type":"array","name":"Mvewn5c7","direction":"output","optional":false}'];
        return $return;
    }

    /**
     * Test JSON decoding on incomplete JSON objects.
     * @param string $json
     * @dataProvider provideIncompleteJsonObjects
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON: phpsap\classes\Api\Struct is missing
     */
    public function testIncompleteJson($json)
    {
        Struct::jsonDecode($json);
    }

    /**
     * Data provider for JSON objects with invalid type value.
     * @return array
     */
    public static function provideJsonDecodeInvalidStruct()
    {
        return [
            ['{"type":"bool","name":"WG0ujcyy","direction":"input","optional":true,'
             .'"members":[{"type":"int","name":"H423vcVw"}]}'],
            ['{"type":"int","name":"TAmi4UC3","direction":"input","optional":true,'
             .'"members":[{"type":"int","name":"TVS46Ay9"}]}'],
            ['{"type":"float","name":"dgDE3yR3","direction":"input","optional":true,'
             .'"members":[{"type":"int","name":"RnlONMhA"}]}'],
            ['{"type":"string","name":"akGRYSzR","direction":"input",'
             .'"optional":true,"members":[{"type":"int","name":"UBrbUzrK"}]}']
        ];
    }

    /**
     * Test JSON decode objects with invalid type value.
     * @param string $json
     * @dataProvider provideJsonDecodeInvalidStruct
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON: API Struct type is not an array!
     */
    public function testJsonDecodeInvalidStruct($json)
    {
        Struct::jsonDecode($json);
    }

    /**
     * Data provider for JSON objects with invalid members.
     * @return array
     */
    public static function provideJsonDecodeInvalidMembers()
    {
        return [
            ['{"type":"array","name":"fiNvZSEH","direction":"output",'
             .'"optional":true,"members":964}'],
            ['{"type":"array","name":"eLkiWCkL","direction":"output",'
             .'"optional":true,"members":5.7}'],
            ['{"type":"array","name":"zF2vTk2P","direction":"output",'
             .'"optional":true,"members":"mKgpyVXb"}'],
            ['{"type":"array","name":"RvU15SUm","direction":"output",'
             .'"optional":true,"members":true}'],
            ['{"type":"array","name":"txI85Gco","direction":"output",'
             .'"optional":true,"members":false}']
        ];
    }

    /**
     * Test JSON decode objects with invalid members.
     * @param string $json
     * @dataProvider provideJsonDecodeInvalidMembers
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON: API Struct members are not an array!
     */
    public function testJsonDecodeInvalidMembers($json)
    {
        Struct::jsonDecode($json);
    }

    /**
     * Test fromArray() using non-array input.
     * @param mixed $input
     * @dataProvider \tests\phpsap\classes\Api\ApiElementTest::provideNonArray
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Expected array, but got
     */
    public function testNonArrayFromArray($input)
    {
        Struct::fromArray($input);
    }
}
