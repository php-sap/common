<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Api;

use phpsap\classes\Api\Member;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IMember;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Class NameTraitTest
 */
class NameTraitTest extends TestCase
{
    /**
     * Data provider for invalid element names.
     * @return array<int, array<int, string>>
     */
    public static function provideInvalidNames(): array
    {
        return [
            [''],
            ["\t"],
        ];
    }

    /**
     * Test invalid element names.
     * @param string $name
     * @throws IInvalidArgumentException
     * @dataProvider provideInvalidNames
     */
    public function testInvalidNames(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API phpsap\classes\Api\Member name to be string!');
        Member::create(IMember::TYPE_STRING, $name);
    }

    /**
     * @return void
     * @throws IInvalidArgumentException
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testValidName(): void
    {
        $member = Member::create(IMember::TYPE_STRING, '28qbnlje');
        static::assertSame('28qbnlje', $member->getName());
    }
}
