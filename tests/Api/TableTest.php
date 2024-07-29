<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Api;

use phpsap\classes\Api\Member;
use phpsap\exceptions\ArrayElementMissingException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IMember;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\exceptions\IArrayElementMissingException;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;
use phpsap\classes\Util\JsonSerializable;
use phpsap\classes\Api\Table;

/**
 * Class tests\phpsap\classes\Api\TableTest
 * @package tests\phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class TableTest extends TestCase
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
        $table = Table::create('cpfNGceT', ITable::DIRECTION_TABLE, true, [
            Member::create(IMember::TYPE_STRING, 'QoTyE1xK')
        ]);
        //heritage
        static::assertInstanceOf(JsonSerializable::class, $table);
        static::assertInstanceOf(ITable::class, $table);
        //basic in-out
        static::assertSame('cpfNGceT', $table->getName());
        static::assertSame(ITable::DIRECTION_TABLE, $table->getDirection());
        static::assertTrue($table->isOptional());
        static::assertSame(ITable::TYPE_TABLE, $table->getType());
        //test members
        $members = $table->getMembers();
        static::assertIsArray($members);
        static::assertCount(1, $members);
        foreach ($members as $member) {
            static::assertInstanceOf(Member::class, $member);
            static::assertSame(IMember::TYPE_STRING, $member->getType());
            static::assertSame('QoTyE1xK', $member->getName());
        }
    }

    /**
     * Test API table members to be instances of IElement.
     * @throws IInvalidArgumentException
     */
    public function testNonIElementMembers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API phpsap\classes\Api\Table members to be instances of phpsap\classes\Api\Member!');
        Table::create('XPn2Pf2n', ITable::DIRECTION_TABLE, true, [new stdClass()]);
    }

    /**
     * Test typecasting of table input.
     * @throws ArrayElementMissingException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testTableCast(): void
    {
        $table = Table::create('kdIriJOc', ITable::DIRECTION_TABLE, true, [
            Member::create(IMember::TYPE_BOOLEAN, '58J0oSzo'),
            Member::create(IMember::TYPE_INTEGER, '1U2pHwPS'),
            Member::create(IMember::TYPE_FLOAT, 'jKPjRoH5'),
            Member::create(IMember::TYPE_STRING, 'aE7iJDWQ')
        ]);
        $raw = [
            [
                '58J0oSzo' => '0',
                '1U2pHwPS' => '351',
                'jKPjRoH5' => '2.8',
                'aE7iJDWQ' => 987257,
                'copU1Tlu' => '2YQTOpvn' //This element is not defined in the API ...
            ],
            [
                '58J0oSzo' => '1',
                '1U2pHwPS' => '81',
                'jKPjRoH5' => '6.82',
                'aE7iJDWQ' => 23.8,
                'copU1Tlu' => '4Qtlfmbh' //This element is not defined in the API ...
            ]
        ];
        $expected = [
            [
                '58J0oSzo' => false,
                '1U2pHwPS' => 351,
                'jKPjRoH5' => 2.8,
                'aE7iJDWQ' => '987257',
                'copU1Tlu' => '2YQTOpvn' //... therefore it stays untouched.
            ],
            [
                '58J0oSzo' => true,
                '1U2pHwPS' => 81,
                'jKPjRoH5' => 6.82,
                'aE7iJDWQ' => '23.8',
                'copU1Tlu' => '4Qtlfmbh' //... therefore it stays untouched.
            ]
        ];
        $actual = $table->cast($raw);
        static::assertSame($expected, $actual);
    }

    /**
     * Test casting raw data with an element missing.
     * @throws InvalidArgumentException
     * @throws IArrayElementMissingException
     */
    public function testTableCastMissingElement(): void
    {
        $table = Table::create('MLOWwQXa', ITable::DIRECTION_TABLE, true, [
            Member::create(IMember::TYPE_STRING, 'IlRdvEQp')
        ]);
        $raw = [
            [
                'dTAcNC0q' => '6NdXXpA4',
                '4Bxn9DJV' => '250'
            ],
            [
                'dTAcNC0q' => 'SDzd9Cy5',
                '4Bxn9DJV' => '311'
            ]
        ];
        $this->expectException(ArrayElementMissingException::class);
        $this->expectExceptionMessage('Element IlRdvEQp in table MLOWwQXa is missing!');
        $table->cast($raw);
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
        $json = '{"type":"table","name":"wWnIOYQc","direction":"table",'
                . '"optional":false,"members":[{"type":"bool","name":"MievIEPs"}]}';
        $element = Table::jsonDecode($json);
        static::assertInstanceOf(Table::class, $element);
        static::assertSame(ITable::TYPE_TABLE, $element->getType());
        static::assertSame('wWnIOYQc', $element->getName());
        static::assertSame(ITable::DIRECTION_TABLE, $element->getDirection());
        static::assertFalse($element->isOptional());
        static::assertIsArray($element->getMembers());
        $members = $element->getMembers();
        foreach ($members as $member) {
            static::assertSame(IMember::TYPE_BOOLEAN, $member->getType());
            static::assertSame('MievIEPs', $member->getName());
        }
    }

    /**
     * Test JSON decoding on incomplete JSON objects.
     * @param string $json
     * @dataProvider \tests\phpsap\classes\Api\StructTest::provideIncompleteJsonObjects()
     */
    public function testIncompleteJson(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON: phpsap\classes\Api\Table is missing');
        Table::jsonDecode($json);
    }

    /**
     * Data provider for JSON objects with invalid type value.
     * @return array
     */
    public static function provideJsonDecodeInvalidType(): array
    {
        return [
            ['{"type":"bool","name":"azc5s1Nb","direction":"table","optional":true,'
             . '"members":[{"type":"int","name":"REFsrhPg"}]}'],
            ['{"type":"int","name":"176PZCKM","direction":"table","optional":true,'
             . '"members":[{"type":"int","name":"BLXWiyhY"}]}'],
            ['{"type":"float","name":"hACHkDDV","direction":"table","optional":true,'
             . '"members":[{"type":"int","name":"CsY4Od9s"}]}'],
            ['{"type":"string","name":"sDbJFlkB","direction":"table",'
             . '"optional":true,"members":[{"type":"int","name":"bVhCMbhQ"}]}']
        ];
    }

    /**
     * Test JSON decode objects with invalid type value.
     * @param string $json
     * @dataProvider provideJsonDecodeInvalidType
     */
    public function testJsonDecodeInvalidType(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API phpsap\classes\Api\Table type to be in: table!');
        Table::jsonDecode($json);
    }

    /**
     * Data provider for JSON objects with invalid members.
     * @return array<int, array<int, string>>
     * @noinspection PhpMethodNamingConventionInspection
     */
    public static function provideJsonDecodeInvalidMembers(): array
    {
        return [
            ['{"type":"table","name":"96JeBc1U","direction":"table",'
             . '"optional":true,"members":[13]}'],
            ['{"type":"table","name":"X1Lw0efh","direction":"table",'
             . '"optional":true,"members":[8.82]}'],
            ['{"type":"table","name":"AdrbBd9G","direction":"table",'
             . '"optional":true,"members":["AbgHgWhx"]}'],
            ['{"type":"table","name":"RvU15SUm","direction":"table",'
             . '"optional":true,"members":[true]}'],
            ['{"type":"table","name":"7LQXw6IT","direction":"table",'
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
        $this->expectExceptionMessage('Invalid JSON: API phpsap\classes\Api\Table members are not an array!');
        Table::jsonDecode($json);
    }
}
