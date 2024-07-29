<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Api;

use JsonSerializable;
use phpsap\classes\Api\Member;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\classes\Api\RemoteApi;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IApi;
use phpsap\interfaces\Api\IApiElement;
use phpsap\interfaces\Api\IMember;
use phpsap\interfaces\Api\IStruct;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\Api\IValue;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class RemoteApiTest
 *
 * @package tests\phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class RemoteApiTest extends TestCase
{
    /**
     * Test for the inherited classes and interfaces.
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInheritance(): void
    {
        $api = new RemoteApi();
        static::assertInstanceOf(RemoteApi::class, $api);
        static::assertInstanceOf(IApi::class, $api);
        static::assertInstanceOf(JsonSerializable::class, $api);
        $json = json_encode($api);
        static::assertSame('[]', $json);
    }

    /**
     * Data provider for API values to add.
     * @return array
     * @throws InvalidArgumentException
     */
    public static function provideApiValue(): array
    {
        return [
            [
                Value::create(IValue::TYPE_STRING, 'cX9WgWXL', IApiElement::DIRECTION_INPUT, true),
                '[{"type":"string","name":"cX9WgWXL","direction":"input","optional":true}]'
            ],
            [
                Struct::create('yLF9w1ss', IApiElement::DIRECTION_OUTPUT, false, [
                    Member::create(IMember::TYPE_STRING, 'QhTuNM8d'),
                    Member::create(IMember::TYPE_INTEGER, '6VpEpxCX')
                ]),
                '[{"type":"struct","name":"yLF9w1ss","direction":"output",'
                . '"optional":false,"members":[{"type":"string","name":"QhTuNM8d"},'
                . '{"type":"int","name":"6VpEpxCX"}]}]'
            ],
            [
                Table::create('506E31r6', ITable::DIRECTION_TABLE, true, [
                    Member::create(IMember::TYPE_FLOAT, 'zrctEv52')
                ]),
                '[{"type":"table","name":"506E31r6","direction":"table",'
                . '"optional":true,"members":[{"type":"float","name":"zrctEv52"}]}]'
            ]
        ];
    }

    /**
     * Test adding API values and compare the JSON encoded output of the remote API.
     * @param Value|Struct|Table $value The value to add.
     * @param string $expected The expected JSON output.
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideApiValue
     */
    public function testAddingAndEncodingApiValue(Value|Struct|Table $value, string $expected): void
    {
        $api = new RemoteApi();
        $api->add($value);
        $actual = json_encode($api);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for encoded remote APIs.
     * @return array
     */
    public static function provideEncodedRemoteApi(): array
    {
        /**
         * Construct first variant as stdClass objects.
         */
        $api1 = [];
        //input value
        $value = new stdClass();
        $value->type = IValue::TYPE_INTEGER;
        $value->name = '1KywmFoU';
        $value->direction = IApiElement::DIRECTION_INPUT;
        $value->optional = false;
        $api1[] = $value;
        //output struct
        $struct = new stdClass();
        $struct->type = IStruct::TYPE_STRUCT;
        $struct->name = 'pS5Irn27';
        $struct->direction = IApiElement::DIRECTION_OUTPUT;
        $struct->optional = false;
        $struct->members = [];
        $member1 = new stdClass();
        $member1->type = IMember::TYPE_FLOAT;
        $member1->name = 't6RVlTkn';
        $struct->members[] = $member1;
        $member2 = new stdClass();
        $member2->type = IMember::TYPE_BOOLEAN;
        $member2->name = 'qjLWHw7O';
        $struct->members[] = $member2;
        $api1[] = $struct;
        //output table
        $table = new stdClass();
        $table->type = ITable::TYPE_TABLE;
        $table->name = 'ZZ4wgCWW';
        $table->direction = ITable::DIRECTION_TABLE;
        $table->optional = false;
        $table->members = [];
        $member1 = new stdClass();
        $member1->type = IMember::TYPE_INTEGER;
        $member1->name = 'GLTKiH2c';
        $table->members[] = $member1;
        $member2 = new stdClass();
        $member2->type = IMember::TYPE_STRING;
        $member2->name = 'Qyjiu3E7';
        $table->members[] = $member2;
        $api1[] = $table;

        /**
         * Construct second variant as array.
         */
        $api2 = [
            [
                'type' => IValue::TYPE_INTEGER,
                'name' => '1KywmFoU',
                'direction' => IApiElement::DIRECTION_INPUT,
                'optional' => false
            ],
            [
                'type' => IStruct::TYPE_STRUCT,
                'name' => 'pS5Irn27',
                'direction' => IApiElement::DIRECTION_OUTPUT,
                'optional' => false,
                'members' => [
                    [
                        'type' => IMember::TYPE_FLOAT,
                        'name' => 't6RVlTkn'
                    ],
                    [
                        'type' => IMember::TYPE_BOOLEAN,
                        'name' => 'qjLWHw7O'
                    ]
                ]
            ],
            [
                'type' => ITable::TYPE_TABLE,
                'name' => 'ZZ4wgCWW',
                'direction' => ITable::DIRECTION_TABLE,
                'optional' => false,
                'members' => [
                    [
                        'type' => IMember::TYPE_INTEGER,
                        'name' => 'GLTKiH2c'
                    ],
                    [
                        'type' => IMember::TYPE_STRING,
                        'name' => 'Qyjiu3E7'
                    ]
                ]
            ]
        ];

        /**
         * Construct third and fourth variants as strings of the above.
         */
        return [
            [json_encode($api1)],
            [json_encode($api2)]
        ];
    }

    /**
     * Test creating API class from an array.
     * @param array|string $config
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideEncodedRemoteApi
     */
    public function testEncodedRemoteApi(array|string $config): void
    {
        $api = RemoteApi::jsonDecode($config);
        static::assertInstanceOf(IApi::class, $api);
        static::assertInstanceOf(RemoteApi::class, $api);
        /**
         * Assert input value.
         */
        $input_values = $api->getInputValues();
        $value = array_pop($input_values);
        static::assertSame(IValue::TYPE_INTEGER, $value->getType());
        static::assertSame('1KywmFoU', $value->getName());
        static::assertSame(IApiElement::DIRECTION_INPUT, $value->getDirection());
        static::assertFalse($value->isOptional());
        $output_values = $api->getOutputValues();
        /**
         * Assert output value.
         * @var Struct $struct
         */
        $struct = array_pop($output_values);
        static::assertSame(IStruct::TYPE_STRUCT, $struct->getType());
        static::assertSame('pS5Irn27', $struct->getName());
        static::assertSame(IApiElement::DIRECTION_OUTPUT, $struct->getDirection());
        static::assertIsArray($struct->getMembers());
        static::assertCount(2, $struct->getMembers());
        $members = $struct->getMembers();
        /** @var Member $member2 */
        $member2 = array_pop($members);
        /** @var Member $member1 */
        $member1 = array_pop($members);
        static::assertSame(IMember::TYPE_FLOAT, $member1->getType());
        static::assertSame('t6RVlTkn', $member1->getName());
        static::assertSame(IMember::TYPE_BOOLEAN, $member2->getType());
        static::assertSame('qjLWHw7O', $member2->getName());
        /**
         * Assert table value.
         */
        $tables = $api->getTables();
        $table = array_pop($tables);
        static::assertSame(ITable::TYPE_TABLE, $table->getType());
        static::assertSame('ZZ4wgCWW', $table->getName());
        static::assertSame(ITable::DIRECTION_TABLE, $table->getDirection());
        static::assertIsArray($table->getMembers());
        static::assertCount(2, $table->getMembers());
        $members = $table->getMembers();
        /** @var Member $member2 */
        $member2 = array_pop($members);
        /** @var Member $member1 */
        $member1 = array_pop($members);
        static::assertSame(IMember::TYPE_INTEGER, $member1->getType());
        static::assertSame('GLTKiH2c', $member1->getName());
        static::assertSame(IMember::TYPE_STRING, $member2->getType());
        static::assertSame('Qyjiu3E7', $member2->getName());
    }

    /**
     * Data provider for invalid JSON.
     * @return array
     */
    public static function provideInvalidJson(): array
    {
        return [
            [''],
            [' '],
            ['{'],
            ['}'],
            ['{"w1sBz6nE":3501'],
            ['DhVsUXYN'],
            ['[{"type":"string","name":"70PSpu7dcO","optional":true}]']
        ];
    }

    /**
     * Test invalid JSON exceptions.
     * @param string $value The value, that will cause an invalid JSON exception.
     * @dataProvider provideInvalidJson
     */
    public function testInvalidJson(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON!');
        RemoteApi::jsonDecode($value);
    }

    /**
     * Test API value with missing type definition.
     */
    public function testApiArrayMissingType(): void
    {
        $def = [
            [
                'name' => 'bvOScFeIOL',
                'direction' => IApiElement::DIRECTION_INPUT,
                'optional' => false
            ]
        ];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API element is missing type.');
        new RemoteApi($def);
    }

    /**
     * Test API value with missing direction definition.
     */
    public function testApiArrayMissingDirection(): void
    {
        $def = [
            [
                'type' => IStruct::TYPE_STRUCT,
                'name' => 'qUvgdjIiMY',
                'optional' => false,
                'members' => []
            ]
        ];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON: phpsap\classes\Api\Struct is missing direction!');
        new RemoteApi($def);
    }
}
