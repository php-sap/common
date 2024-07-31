<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Api\Traits;

use phpsap\classes\Api\Member;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IMember;
use phpsap\interfaces\Api\IStruct;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Class TypeTraitTest
 */
class TypeTraitTest extends TestCase
{
    /**
     * Data provider for invalid element types.
     * @return array<int, array<int, string>>
     */
    public static function provideInvalidElementTypes(): array
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
     * @param string $type
     * @return void
     * @throws IInvalidArgumentException
     * @dataProvider provideInvalidElementTypes
     */
    public function testInvalidType(string $type): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API phpsap\classes\Api\Member type to be in:');
        Member::create($type, '9dnjz4WD');
    }

    /**
     * Data provider for valid element types.
     * @return array<int, array<int, string>>
     */
    public static function provideValidTypes(): array
    {
        return [
            [IMember::TYPE_BOOLEAN],
            [IMember::TYPE_INTEGER],
            [IMember::TYPE_FLOAT],
            [IMember::TYPE_STRING]
        ];
    }

    /**
     * Test valid element types.
     * @param string $type
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideValidTypes
     */
    public function testValidTypes(string $type): void
    {
        $element = Member::create($type, 'D6vT5LVY');
        static::assertSame($type, $element->getType());
    }
}
