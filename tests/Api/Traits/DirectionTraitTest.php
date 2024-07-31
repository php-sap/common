<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Api\Traits;

use phpsap\classes\Api\Value;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IApiElement;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\Api\IValue;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Class DirectionTraitTest
 */
class DirectionTraitTest extends TestCase
{
    /**
     * Data provider for invalid direction strings.
     * @return array<int, array<int, string>>
     */
    public static function provideInvalidDirections(): array
    {
        return [
            [''],
            ['INPUT'],
            ['OuTpUt'],
            ['Table'],
            ['in'],
            ['out'],
            [ITable::DIRECTION_TABLE]
        ];
    }

    /**
     * Test invalid direction strings.
     * @param string $direction
     * @throws IInvalidArgumentException
     * @dataProvider provideInvalidDirections
     */
    public function testInvalidDirections(string $direction): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API phpsap\classes\Api\Value direction to be in:');
        Value::create(IValue::TYPE_STRING, '5spuxoq6', $direction, false);
    }

    /**
     * Data provider for valid direction strings.
     * @return array<int, array<int, string>>
     */
    public static function provideValidDirections(): array
    {
        return [
            [IApiElement::DIRECTION_INPUT],
            [IApiElement::DIRECTION_OUTPUT],
        ];
    }

    /**
     * Test valid directions.
     * @param string $direction
     * @return void
     * @throws IInvalidArgumentException
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideValidDirections
     */
    public function testValidDirections(string $direction): void
    {
        $value = Value::create(IValue::TYPE_STRING, '4nr2zjru', $direction, false);
        static::assertSame($direction, $value->getDirection());
    }
}
