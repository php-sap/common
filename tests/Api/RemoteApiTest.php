<?php

namespace tests\phpsap\classes;

use JsonSerializable;
use phpsap\classes\Api\Element;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\classes\Api\RemoteApi;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IApi;
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
    public function testInheritance()
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
                new Value(Value::TYPE_STRING, 'cX9WgWXL', Value::DIRECTION_INPUT, true),
                '[{"type":"string","name":"cX9WgWXL","direction":"input","optional":true}]'
            ],
            [
                new Struct('yLF9w1ss', Struct::DIRECTION_OUTPUT, false, [
                    new Element(Element::TYPE_STRING, 'QhTuNM8d'),
                    new Element(Element::TYPE_INTEGER, '6VpEpxCX')
                ]),
                '[{"type":"struct","name":"yLF9w1ss","direction":"output",'
                . '"optional":false,"members":[{"type":"string","name":"QhTuNM8d"},'
                . '{"type":"int","name":"6VpEpxCX"}]}]'
            ],
            [
                new Table('506E31r6', Table::DIRECTION_TABLE, true, [
                    new Element(Element::TYPE_FLOAT, 'zrctEv52')
                ]),
                '[{"type":"table","name":"506E31r6","direction":"table",'
                . '"optional":true,"members":[{"type":"float","name":"zrctEv52"}]}]'
            ]
        ];
    }

    /**
     * Test adding API values and compare the JSON encoded output of the remote API.
     * @param IValue $value The value to add.
     * @param string $expected The expected JSON output.
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideApiValue
     */
    public function testAddingAndEncodingApiValue($value, $expected)
    {
        $api = new RemoteApi();
        $api->add($value);
        $actual = json_encode($api);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for invalid constructor parameters.
     * @return array
     */
    public static function provideInvalidConstructorParams(): array
    {
        return [
            [''],
            ['[]'],
            ['{}'],
            ['dJwVRbnAsy'],
            [83111],
            [1.99],
            [true],
            [false],
            [new stdClass()]
        ];
    }

    /**
     * Test invalid constructor parameters.
     * @param mixed $input
     * @dataProvider provideInvalidConstructorParams
     */
    public function testInvalidConstructorParams($input)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected array of API values.');
        new RemoteApi($input);
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
        $value->type = Value::TYPE_INTEGER;
        $value->name = '1KywmFoU';
        $value->direction = Value::DIRECTION_INPUT;
        $value->optional = false;
        $api1[] = $value;
        //output struct
        $struct = new stdClass();
        $struct->type = Struct::TYPE_STRUCT;
        $struct->name = 'pS5Irn27';
        $struct->direction = Struct::DIRECTION_OUTPUT;
        $struct->optional = false;
        $struct->members = [];
        $member1 = new stdClass();
        $member1->type = Element::TYPE_FLOAT;
        $member1->name = 't6RVlTkn';
        $struct->members[] = $member1;
        $member2 = new stdClass();
        $member2->type = Element::TYPE_BOOLEAN;
        $member2->name = 'qjLWHw7O';
        $struct->members[] = $member2;
        $api1[] = $struct;
        //output table
        $table = new stdClass();
        $table->type = Table::TYPE_TABLE;
        $table->name = 'ZZ4wgCWW';
        $table->direction = Table::DIRECTION_TABLE;
        $table->optional = false;
        $table->members = [];
        $member1 = new stdClass();
        $member1->type = Element::TYPE_INTEGER;
        $member1->name = 'GLTKiH2c';
        $table->members[] = $member1;
        $member2 = new stdClass();
        $member2->type = Element::TYPE_STRING;
        $member2->name = 'Qyjiu3E7';
        $table->members[] = $member2;
        $api1[] = $table;

        /**
         * Construct second variant as array.
         */
        $api2 = [
            [
                'type' => Value::TYPE_INTEGER,
                'name' => '1KywmFoU',
                'direction' => Value::DIRECTION_INPUT,
                'optional' => false
            ],
            [
                'type' => Struct::TYPE_STRUCT,
                'name' => 'pS5Irn27',
                'direction' => Struct::DIRECTION_OUTPUT,
                'optional' => false,
                'members' => [
                    [
                        'type' => Element::TYPE_FLOAT,
                        'name' => 't6RVlTkn'
                    ],
                    [
                        'type' => Element::TYPE_BOOLEAN,
                        'name' => 'qjLWHw7O'
                    ]
                ]
            ],
            [
                'type' => Table::TYPE_TABLE,
                'name' => 'ZZ4wgCWW',
                'direction' => Table::DIRECTION_TABLE,
                'optional' => false,
                'members' => [
                    [
                        'type' => Element::TYPE_INTEGER,
                        'name' => 'GLTKiH2c'
                    ],
                    [
                        'type' => Element::TYPE_STRING,
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
    public function testEncodedRemoteApi($config)
    {
        $api = RemoteApi::jsonDecode($config);
        static::assertInstanceOf(IApi::class, $api);
        static::assertInstanceOf(RemoteApi::class, $api);
        /**
         * Assert input value.
         */
        $inputValues = $api->getInputValues();
        $value = array_pop($inputValues);
        static::assertSame(Value::TYPE_INTEGER, $value->getType());
        static::assertSame('1KywmFoU', $value->getName());
        static::assertSame(Value::DIRECTION_INPUT, $value->getDirection());
        static::assertFalse($value->isOptional());
        /**
         * Assert output value.
         * @var Struct $struct
         */
        $outputValues = $api->getOutputValues();
        $struct = array_pop($outputValues);
        static::assertSame(Struct::TYPE_STRUCT, $struct->getType());
        static::assertSame('pS5Irn27', $struct->getName());
        static::assertSame(Struct::DIRECTION_OUTPUT, $struct->getDirection());
        static::assertIsArray($struct->getMembers());
        static::assertCount(2, $struct->getMembers());
        /**
         * Assert output struct members.
         * @var Element $member1
         * @var Element $member2
         * Explanation: array_pop() takes the last element out first (LIFO).
         */
        $members = $struct->getMembers();
        $member2 = array_pop($members);
        $member1 = array_pop($members);
        static::assertSame(Element::TYPE_FLOAT, $member1->getType());
        static::assertSame('t6RVlTkn', $member1->getName());
        static::assertSame(Element::TYPE_BOOLEAN, $member2->getType());
        static::assertSame('qjLWHw7O', $member2->getName());
        /**
         * Assert table value.
         */
        $tables = $api->getTables();
        $table = array_pop($tables);
        static::assertSame(Table::TYPE_TABLE, $table->getType());
        static::assertSame('ZZ4wgCWW', $table->getName());
        static::assertSame(Table::DIRECTION_TABLE, $table->getDirection());
        static::assertIsArray($table->getMembers());
        static::assertCount(2, $table->getMembers());
        /**
         * Assert table members.
         * @var Element $member1
         * @var Element $member2
         * Explanation: array_pop() takes the last element out first (LIFO).
         */
        $members = $table->getMembers();
        $member2 = array_pop($members);
        $member1 = array_pop($members);
        static::assertSame(Element::TYPE_INTEGER, $member1->getType());
        static::assertSame('GLTKiH2c', $member1->getName());
        static::assertSame(Element::TYPE_STRING, $member2->getType());
        static::assertSame('Qyjiu3E7', $member2->getName());
    }

    /**
     * Data provider for invalid JSON.
     * @return array
     */
    public static function provideInvalidJson(): array
    {
        $table = new stdClass();
        $table->type = Table::TYPE_TABLE;
        return [
            [''],
            [' '],
            ['{'],
            ['}'],
            ['{"w1sBz6nE":3501'],
            ['DhVsUXYN'],
            [339],
            [2.3],
            [true],
            [false],
            [new stdClass()],
            [$table],
            ['[{"type":"string","name":"70PSpu7dcO","optional":true}]']
        ];
    }

    /**
     * Test invalid JSON exceptions.
     * @param mixed $value The value, that will cause an invalid JSON exception.
     * @dataProvider provideInvalidJson
     */
    public function testInvalidJson($value)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON!');
        RemoteApi::jsonDecode($value);
    }

    /**
     * Test API value with missing type definition.
     */
    public function testApiArrayMissingType()
    {
        $def = [
            [
                'name' => 'bvOScFeIOL',
                'direction' => Value::DIRECTION_INPUT,
                'optional' => false
            ]
        ];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('API Value is missing type.');
        new RemoteApi($def);
    }

    /**
     * Test API value with missing direction definition.
     */
    public function testApiArrayMissingDirection()
    {
        $def = [
            [
                'type' => Struct::TYPE_STRUCT,
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
