<?php

namespace tests\phpsap\classes\Api;

use phpsap\exceptions\ArrayElementMissingException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IStruct;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
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
class StructTest extends TestCase
{
    /**
     * Test the constructor and the inherited classes and interfaces.
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testConstructorAndInheritance()
    {
        $struct = new Struct('9rlCO8It', Struct::DIRECTION_INPUT, false, [
            new Element(Element::TYPE_STRING, 'K82B6qoL')
        ]);
        //heritage
        static::assertInstanceOf(JsonSerializable::class, $struct);
        static::assertInstanceOf(IStruct::class, $struct);
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
        static::assertSame(Struct::TYPE_STRUCT, $struct->getType());
        //test members
        $members = $struct->getMembers();
        static::assertIsArray($members);
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
     */
    public function testNonArrayMembers($members)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API struct members to be in an array!');
        new Struct('9rlCO8It', Struct::DIRECTION_INPUT, false, $members);
    }

    /**
     * Test typecasting of struct input
     * @throws ArrayElementMissingException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
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
     * @throws InvalidArgumentException
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
        $this->expectException(ArrayElementMissingException::class);
        $this->expectExceptionMessage('Element pUDY31My in struct 1M9enD5H is missing!');
        $struct->cast($raw);
    }

    /**
     * Test adding non-IElement members.
     */
    public function testNonIElementMembers()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API struct members to be instances of IElement!');
        new Struct('1M9enD5H', Struct::DIRECTION_OUTPUT, true, [new stdClass()]);
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
        $json = '{"type":"struct","name":"l9M7gn6p","direction":"input",'
                . '"optional":true,"members":[{"type":"int","name":"lnrxpRjh"}]}';
        $element = Struct::jsonDecode($json);
        static::assertInstanceOf(Struct::class, $element);
        static::assertSame(Struct::TYPE_STRUCT, $element->getType());
        static::assertSame('l9M7gn6p', $element->getName());
        static::assertSame(Struct::DIRECTION_INPUT, $element->getDirection());
        static::assertTrue($element->isOptional());
        static::assertIsArray($element->getMembers());
        $members = $element->getMembers();
        foreach ($members as $member) {
            /**
             * @var IElement $member
             */
            static::assertSame(Struct::TYPE_INTEGER, $member->getType());
            static::assertSame('lnrxpRjh', $member->getName());
        }
    }

    /**
     * Test JSON decoding on invalid parameters.
     * @param mixed $json
     * @dataProvider \tests\phpsap\classes\Api\ApiElementTest::provideInvalidJson()
     */
    public function testInvalidJson($json)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON!');
        Struct::jsonDecode($json);
    }

    /**
     * Data provider for incomplete JSON objects.
     * @return array
     */
    public static function provideIncompleteJsonObjects()
    {
        $return = ApiValueTest::provideIncompleteJsonObjects();
        $return[] = ['{"type":"struct","name":"Mvewn5c7","direction":"output","optional":false}'];
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
        $this->expectExceptionMessage('Invalid JSON: phpsap\classes\Api\Struct is missing');
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
             . '"members":[{"type":"int","name":"H423vcVw"}]}'],
            ['{"type":"int","name":"TAmi4UC3","direction":"input","optional":true,'
             . '"members":[{"type":"int","name":"TVS46Ay9"}]}'],
            ['{"type":"float","name":"dgDE3yR3","direction":"input","optional":true,'
             . '"members":[{"type":"int","name":"RnlONMhA"}]}'],
            ['{"type":"string","name":"akGRYSzR","direction":"input",'
             . '"optional":true,"members":[{"type":"int","name":"UBrbUzrK"}]}']
        ];
    }

    /**
     * Test JSON decode objects with invalid type value.
     * @param string $json
     * @dataProvider provideJsonDecodeInvalidStruct
     */
    public function testJsonDecodeInvalidStruct($json)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON: API Struct type is not an array!');
        Struct::jsonDecode($json);
    }

    /**
     * Data provider for JSON objects with invalid members.
     * @return array
     */
    public static function provideJsonDecodeInvalidMembers()
    {
        return [
            ['{"type":"struct","name":"fiNvZSEH","direction":"output",'
             . '"optional":true,"members":964}'],
            ['{"type":"struct","name":"eLkiWCkL","direction":"output",'
             . '"optional":true,"members":5.7}'],
            ['{"type":"struct","name":"zF2vTk2P","direction":"output",'
             . '"optional":true,"members":"mKgpyVXb"}'],
            ['{"type":"struct","name":"RvU15SUm","direction":"output",'
             . '"optional":true,"members":true}'],
            ['{"type":"struct","name":"txI85Gco","direction":"output",'
             . '"optional":true,"members":false}']
        ];
    }

    /**
     * Test JSON decode objects with invalid members.
     * @param string $json
     * @dataProvider provideJsonDecodeInvalidMembers
     */
    public function testJsonDecodeInvalidMembers($json)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON: API Struct members are not an array!');
        Struct::jsonDecode($json);
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
        Struct::fromArray($input);
    }
}
