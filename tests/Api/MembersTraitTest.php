<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Api;

use phpsap\classes\Api\Member;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IApiElement;
use phpsap\interfaces\Api\IMember;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class MembersTraitTest
 */
class MembersTraitTest extends TestCase
{
    /**
     * Test valid members of a Struct.
     * @return void
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws IInvalidArgumentException
     */
    public function testValidMembers(): void
    {
        $members = [
            Member::create(IMember::TYPE_BOOLEAN, 'Rt4CXgff')
        ];
        $struct = Struct::create('90zheeon', IApiElement::DIRECTION_INPUT, false, $members);
        static::assertCount(1, $struct->getMembers());
        static::assertSame('Rt4CXgff', $struct->getMembers()[0]->getName());
    }

    /**
     * Test invalid members of a struct.
     * @return void
     * @throws IInvalidArgumentException
     */
    public function testInvalidMembers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected API phpsap\classes\Api\Table members to be instances of phpsap\classes\Api\Member!');
        Table::create('bNqp97b6', ITable::DIRECTION_TABLE, false, [new stdClass()]);
    }
}
