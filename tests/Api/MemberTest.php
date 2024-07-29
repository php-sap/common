<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Api;

use phpsap\classes\Api\Member;
use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IMember;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Class MemberTest
 */
class MemberTest extends TestCase
{
    /**
     * @return void
     * @throws ExpectationFailedException
     * @throws IInvalidArgumentException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testCreate(): void
    {
        $member = Member::create(IMember::TYPE_STRING, 'pG545XSy');
        static::assertInstanceOf(IMember::class, $member);
        static::assertInstanceOf(JsonSerializable::class, $member);
        static::assertInstanceOf(Member::class, $member);
        static::assertSame(IMember::TYPE_STRING, $member->getType());
        static::assertSame('pG545XSy', $member->getName());
    }

    /**
     * Test JSON serializing an element class.
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws IInvalidArgumentException
     */
    public function testJsonSerialize(): void
    {
        $member = Member::create(IMember::TYPE_INTEGER, 'fcotBFjX');
        $expected = '{"type":"int","name":"fcotBFjX"}';
        static::assertSame($expected, json_encode($member));
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
        $member = Member::jsonDecode('{"type":"string","name":"VaJPAA7d"}');
        static::assertInstanceOf(Member::class, $member);
        static::assertSame(IMember::TYPE_STRING, $member->getType());
        static::assertSame('VaJPAA7d', $member->getName());
    }

    /**
     * Data provider for values, that won't JSON decode to the expected configuration
     * array.
     * @return array<int, array<int, string>
     */
    public static function provideInvalidJsonString(): array
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
     * @param string $json
     * @dataProvider provideInvalidJsonString
     */
    public function testInvalidJsonString(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON! Expected JSON encoded phpsap\classes\Api\Member string!');
        Member::jsonDecode($json);
    }

    /**
     * Data provider for incomplete JSON objects.
     * @return array<int, array<int, string>>
     */
    public static function provideIncompleteJsonObjects(): array
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
     */
    public function testIncompleteJsonObjects(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON: phpsap\classes\Api\Member is missing');
        Member::jsonDecode($json);
    }
}
