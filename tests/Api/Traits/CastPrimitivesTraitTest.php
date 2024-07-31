<?php

/** @noinspection PhpClassNamingConventionInspection */

declare(strict_types=1);

namespace tests\phpsap\classes\Api\Traits;

use DateInterval;
use DateTime;
use phpsap\classes\Api\Member;
use phpsap\interfaces\Api\IMember;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Class CastPrimitivesTraitTest
 */
class CastPrimitivesTraitTest extends TestCase
{
    /**
     * @return array<int, array<int, null|bool|float|int|string|DateTime|DateInterval>>
     */
    public static function provideCastPrimitives(): array
    {
        return [
            [IMember::TYPE_DATE, '20191030', DateTime::createFromFormat('Y-m-d H:i:s', '2019-10-30 00:00:00')],
            [IMember::TYPE_DATE, '00000000', null],
            [IMember::TYPE_TIME, '102030', new DateInterval('PT10H20M30S')],
            [IMember::TYPE_TIMESTAMP, '20191030102030', DateTime::createFromFormat('Y-m-d H:i:s', '2019-10-30 10:20:30')],
            [IMember::TYPE_WEEK, '201944', new DateTime('2019W44')],
            [IMember::TYPE_HEXBIN, '534150', 'SAP'],
            [IMember::TYPE_BOOLEAN, '1', true],
            [IMember::TYPE_BOOLEAN, '0', false],
            [IMember::TYPE_INTEGER, '98', 98],
            [IMember::TYPE_FLOAT, '5.7', 5.7],
            [IMember::TYPE_STRING, 21, '21'],
        ];
    }

    /**
     * @param string $type
     * @param bool|float|int|string $input
     * @param null|bool|float|int|string|DateTime|DateInterval $expected
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws IInvalidArgumentException
     * @dataProvider provideCastPrimitives
     */
    public function testCastPrimitives(string $type, bool|float|int|string $input, null|bool|float|int|string|DateTime|DateInterval $expected)
    {
        $actual = Member::create($type, 'eoVBbCVO')->cast($input);
        static::assertEquals($expected, $actual);
    }
}
