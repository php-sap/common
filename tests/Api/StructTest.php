<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Api;

use phpsap\classes\Api\Member;
use phpsap\exceptions\ArrayElementMissingException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IApiElement;
use phpsap\interfaces\Api\IMember;
use phpsap\interfaces\Api\IStruct;
use phpsap\interfaces\exceptions\IArrayElementMissingException;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;
use phpsap\classes\Util\JsonSerializable;
use phpsap\classes\Api\Struct;

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
    public function testConstructorAndInheritance(): void
    {
        $struct = Struct::create('9rlCO8It', IApiElement::DIRECTION_INPUT, false, [
            Member::create(IMember::TYPE_STRING, 'K82B6qoL')
        ]);
        //heritage
        static::assertInstanceOf(JsonSerializable::class, $struct);
        static::assertInstanceOf(IStruct::class, $struct);
        //basic in-out
        static::assertSame('9rlCO8It', $struct->getName());
        static::assertSame(IApiElement::DIRECTION_INPUT, $struct->getDirection());
        static::assertFalse($struct->isOptional());
        static::assertSame(IStruct::TYPE_STRUCT, $struct->getType());
        //test members
        $members = $struct->getMembers();
        static::assertIsArray($members);
        static::assertCount(1, $members);
        foreach ($members as $member) {
            static::assertInstanceOf(Member::class, $member);
            static::assertSame(IMember::TYPE_STRING, $member->getType());
            static::assertSame('K82B6qoL', $member->getName());
        }
    }

    /**
     * Test typecasting of struct input
     * @throws ArrayElementMissingException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testStructCast(): void
    {
        $struct = Struct::create('HVfgbemt', IApiElement::DIRECTION_INPUT, false, [
            Member::create(IMember::TYPE_BOOLEAN, 'UmYDghzf'),
            Member::create(IMember::TYPE_INTEGER, 'xH9lLz9D'),
            Member::create(IMember::TYPE_FLOAT, 'p6ZMXkFG'),
            Member::create(IMember::TYPE_STRING, '1Kdi4MOO')
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
     * @throws IArrayElementMissingException
     */
    public function testStructCastMissingElement(): void
    {
        $struct = Struct::create('1M9enD5H', IApiElement::DIRECTION_OUTPUT, true, [
            Member::create(IMember::TYPE_STRING, 'pUDY31My')
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
     * Test adding non-Member members.
     * @throws IInvalidArgumentException
     */
    public function testNonMembers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Expected API phpsap\classes\Api\Struct members to be instances of phpsap\classes\Api\Member!'
        );
        Struct::create('1M9enD5H', IApiElement::DIRECTION_OUTPUT, true, [new stdClass()]);
    }

    /**
     * Test JSON decode.
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testJsonDecode(): void
    {
        $json = '{"type":"struct","name":"l9M7gn6p","direction":"input",'
                . '"optional":true,"members":[{"type":"int","name":"lnrxpRjh"}]}';
        $element = Struct::jsonDecode($json);
        static::assertInstanceOf(Struct::class, $element);
        static::assertSame(IStruct::TYPE_STRUCT, $element->getType());
        static::assertSame('l9M7gn6p', $element->getName());
        static::assertSame(IApiElement::DIRECTION_INPUT, $element->getDirection());
        static::assertTrue($element->isOptional());
        static::assertIsArray($element->getMembers());
        $members = $element->getMembers();
        foreach ($members as $member) {
            static::assertSame(IMember::TYPE_INTEGER, $member->getType());
            static::assertSame('lnrxpRjh', $member->getName());
        }
    }

    /**
     * Data provider for incomplete JSON objects.
     * @return array<int, array<int, string>>
     */
    public static function provideIncompleteJsonObjects(): array
    {
        $return = ValueTest::provideIncompleteJsonObjects();
        $return[] = ['{"type":"struct","name":"Mvewn5c7","direction":"output","optional":false}'];
        return $return;
    }

    /**
     * Test JSON decoding on incomplete JSON objects.
     * @param string $json
     * @dataProvider provideIncompleteJsonObjects
     */
    public function testIncompleteJson(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON:');
        Struct::jsonDecode($json);
    }

    /**
     * Data provider for JSON objects with invalid type value.
     * @return array<int, array<int, string>>
     */
    public static function provideJsonDecodeInvalidStruct(): array
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
    public function testJsonDecodeInvalidStruct(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON:');
        Struct::jsonDecode($json);
    }

    /**
     * Data provider for JSON objects with invalid members.
     * @return array<int, array<int, string>>
     * @noinspection PhpMethodNamingConventionInspection
     */
    public static function provideJsonDecodeInvalidMembers(): array
    {
        return [
            ['{"type":"struct","name":"fiNvZSEH","direction":"output",'
             . '"optional":true,"members":[964]}'],
            ['{"type":"struct","name":"eLkiWCkL","direction":"output",'
             . '"optional":true,"members":[5.7]}'],
            ['{"type":"struct","name":"zF2vTk2P","direction":"output",'
             . '"optional":true,"members":["mKgpyVXb"]}'],
            ['{"type":"struct","name":"RvU15SUm","direction":"output",'
             . '"optional":true,"members":[true]}'],
            ['{"type":"struct","name":"txI85Gco","direction":"output",'
             . '"optional":true,"members":[false]}']
        ];
    }

    /**
     * Test JSON decode objects with invalid members.
     * @param string $json
     * @dataProvider provideJsonDecodeInvalidMembers
     */
    public function testJsonDecodeInvalidMembers(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON:');
        Struct::jsonDecode($json);
    }
}
