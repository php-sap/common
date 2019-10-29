<?php

namespace tests\phpsap\classes;

use JsonSerializable;
use phpsap\classes\Api\Element;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\classes\RemoteApi;
use phpsap\interfaces\IApi;
use stdClass;

/**
 * Class tests\phpsap\classes\RemoteApiTest
 *
 * @package tests\phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
class RemoteApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for the inherited classes and interfaces.
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
     */
    public static function provideApiValue()
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
                '[{"type":"array","name":"yLF9w1ss","direction":"output",'
                . '"optional":false,"members":[{"type":"string","name":"QhTuNM8d"},'
                . '{"type":"int","name":"6VpEpxCX"}]}]'
            ],
            [
                new Table('506E31r6', true, [
                    new Element(Element::TYPE_FLOAT, 'zrctEv52')
                ]),
                '[{"type":"array","name":"506E31r6","direction":"table",'
                . '"optional":true,"members":[{"type":"float","name":"zrctEv52"}]}]'
            ]
        ];
    }

    /**
     * Test adding API values and compare the JSON encoded output of the remote API.
     * @param \phpsap\interfaces\Api\IValue $value The value to add.
     * @param string $expected The expected JSON output.
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
     * Data provider for encoded remote APIs.
     * @return array
     */
    public static function provideEncodedRemoteApi()
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
        $struct->type = Struct::TYPE_ARRAY;
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
        $table->type = Table::TYPE_ARRAY;
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
                'type' => Struct::TYPE_ARRAY,
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
                'type' => Table::TYPE_ARRAY,
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
        $api3 = json_encode($api1);
        $api4 = json_encode($api2);
        return [
            [$api1],
            [$api2],
            [$api3],
            [$api4]
        ];
    }

    /**
     * Test creating API class from encoded API class.
     * @param string|array|\stdClass $json
     * @dataProvider provideEncodedRemoteApi
     */
    public function testEncodedRemoteApi($json)
    {
        $api = new RemoteApi($json);
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
         * @var \phpsap\classes\Api\Struct $struct
         */
        $outputValues = $api->getOutputValues();
        $struct = array_pop($outputValues);
        static::assertSame(Struct::TYPE_ARRAY, $struct->getType());
        static::assertSame('pS5Irn27', $struct->getName());
        static::assertSame(Struct::DIRECTION_OUTPUT, $struct->getDirection());
        static::assertInternalType('array', $struct->getMembers());
        static::assertCount(2, $struct->getMembers());
        /**
         * Assert output struct members.
         * @var \phpsap\classes\Api\Element $member1
         * @var \phpsap\classes\Api\Element $member2
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
        static::assertSame(Table::TYPE_ARRAY, $table->getType());
        static::assertSame('ZZ4wgCWW', $table->getName());
        static::assertSame(Table::DIRECTION_TABLE, $table->getDirection());
        static::assertInternalType('array', $table->getMembers());
        static::assertCount(2, $table->getMembers());
        /**
         * Assert table members.
         * @var \phpsap\classes\Api\Element $member1
         * @var \phpsap\classes\Api\Element $member2
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
    public static function provideInvalidJson()
    {
        $table = new stdClass();
        $table->type = Table::TYPE_ARRAY;
        return [
            ['DhVsUXYN'],
            [339],
            [2.3],
            [true],
            [false],
            [new stdClass()],
            [['mn0EwF4z']],
            [[50]],
            [[71.2]],
            [[true]],
            [[false]],
            [[null]],
            [[new stdClass()]],
            [['type' => Struct::TYPE_ARRAY]],
            [[$table]]
        ];
    }

    /**
     * Test invalid JSON exceptions.
     * @param mixed $value The value, that will cause an invalid JSON exception.
     * @dataProvider provideInvalidJson
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON:
     */
    public function testInvalidJson($value)
    {
        RemoteApi::jsonDecode($value);
    }

    /**
     * Test type casting using the remote API.
     */
    public function testTypeCast()
    {
        $api = new RemoteApi([
            [
                'type' => Value::TYPE_INTEGER,
                'name' => 'RLvECJCP',
                'direction' => Value::DIRECTION_INPUT,
                'optional' => false
            ],
            [
                'type' => Struct::TYPE_ARRAY,
                'name' => 'DzyWmfLC',
                'direction' => Struct::DIRECTION_OUTPUT,
                'optional' => false,
                'members' => [
                    [
                        'type' => Element::TYPE_FLOAT,
                        'name' => 'd5GidI8D'
                    ],
                    [
                        'type' => Element::TYPE_BOOLEAN,
                        'name' => 'XXTXVpI0'
                    ]
                ]
            ],
            [
                'type' => Table::TYPE_ARRAY,
                'name' => 'RmVSLkyK',
                'direction' => Table::DIRECTION_TABLE,
                'optional' => false,
                'members' => [
                    [
                        'type' => Element::TYPE_INTEGER,
                        'name' => 'UlehUlYF'
                    ],
                    [
                        'type' => Element::TYPE_STRING,
                        'name' => 'AiQ2oCoy'
                    ]
                ]
            ]
        ]);

        /**
         * Test typecasting input values.
         */
        $actualInput = $api->castInputValues([
            'RLvECJCP' => '593'
        ]);
        static::assertInternalType('array', $actualInput);
        static::assertArrayHasKey('RLvECJCP', $actualInput);
        static::assertSame(593, $actualInput['RLvECJCP']);
        /**
         * Test typecasting output values.
         */
        $output = [
            'DzyWmfLC' => [
                'd5GidI8D' => '2.86',
                'XXTXVpI0' => '1'
            ],
            'RmVSLkyK' => [
                [
                    'UlehUlYF' => '186',
                    'AiQ2oCoy' => 748367
                ],
                [
                    'UlehUlYF' => '330',
                    'AiQ2oCoy' => 483996
                ]
            ]
        ];
        $actualOutput = $api->castOutputValues($output);
        static::assertInternalType('array', $actualOutput);
        static::assertArrayHasKey('DzyWmfLC', $actualOutput);
        static::assertInternalType('array', $actualOutput['DzyWmfLC']);
        static::assertArrayHasKey('d5GidI8D', $actualOutput['DzyWmfLC']);
        static::assertArrayHasKey('XXTXVpI0', $actualOutput['DzyWmfLC']);
        static::assertSame(2.86, $actualOutput['DzyWmfLC']['d5GidI8D']);
        static::assertTrue($actualOutput['DzyWmfLC']['XXTXVpI0']);

        $actualTable = $api->castTables($output);
        static::assertInternalType('array', $actualTable);
        static::assertArrayHasKey('RmVSLkyK', $actualTable);
        static::assertInternalType('array', $actualTable['RmVSLkyK']);
        static::assertCount(2, $actualTable['RmVSLkyK']);
        $actualRow2 = array_pop($actualTable['RmVSLkyK']);
        $actualRow1 = array_pop($actualTable['RmVSLkyK']);
        static::assertInternalType('array', $actualRow1);
        static::assertInternalType('array', $actualRow2);
        static::assertArrayHasKey('UlehUlYF', $actualRow1);
        static::assertArrayHasKey('UlehUlYF', $actualRow2);
        static::assertArrayHasKey('AiQ2oCoy', $actualRow1);
        static::assertArrayHasKey('AiQ2oCoy', $actualRow2);
        static::assertSame(186, $actualRow1['UlehUlYF']);
        static::assertSame(330, $actualRow2['UlehUlYF']);
        static::assertSame('748367', $actualRow1['AiQ2oCoy']);
        static::assertSame('483996', $actualRow2['AiQ2oCoy']);
    }

    /**
     * Data provider for invalid values for typecasting.
     * @return array
     */
    public static function provideInvalidTypecastValue()
    {
        return [
            [826],
            [3.7],
            [true],
            [false],
            [null],
            [new stdClass()]
        ];
    }

    /**
     * Test typecasting invalid values.
     * @param mixed $value
     * @dataProvider provideInvalidTypeCastValue
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected data for typecasting to be an array!
     */
    public function testInvalidTypecastValue($value)
    {
        $api = new RemoteApi();
        $api->castInputValues($value);
    }

    /**
     * Test missing mandatory value for typecasting.
     * @expectedException \phpsap\exceptions\ArrayElementMissingException
     * @expectedExceptionMessage Mandatory input value PM1cKiSw is missing!
     */
    public function testMissingMandatoryValueForTypecasting()
    {
        $api = new RemoteApi([
            [
                'type' => Value::TYPE_INTEGER,
                'name' => 'PM1cKiSw',
                'direction' => Value::DIRECTION_INPUT,
                'optional' => false
            ]
        ]);
        $api->castInputValues([]);
    }
}
