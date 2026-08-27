<?php

declare(strict_types=1);

namespace tests\phpsap\classes;

use phpsap\classes\AbstractFunction;
use phpsap\classes\Api\RemoteApi;
use phpsap\classes\Api\Value;
use phpsap\classes\Config\ConfigTypeA;
use phpsap\classes\Config\ConfigTypeB;
use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\Api\IApiElement;
use phpsap\interfaces\Api\IMember;
use phpsap\interfaces\Api\IStruct;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\Api\IValue;
use phpsap\interfaces\exceptions\IConnectionFailedException;
use phpsap\interfaces\exceptions\IIncompleteConfigException;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use phpsap\interfaces\exceptions\IUnknownFunctionException;
use phpsap\interfaces\IFunction;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;
use tests\phpsap\classes\helper\AbstractFunctionInstance;

/**
 * Class tests\phpsap\classes\AbstractFunctionTest
 *
 * Test the AbstractFunction class.
 *
 * @package tests\phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
class AbstractFunctionTest extends TestCase
{
    /**
     * Test class inheritance.
     */
    public function testInheritance(): void
    {
        $fnc = AbstractFunctionInstance::create('QifKTqzu');
        static::assertInstanceOf(IFunction::class, $fnc);
        static::assertInstanceOf(JsonSerializable::class, $fnc);
    }

    /**
     * Data provider for invalid function names.
     * @return array<int, array<int, string>>
     */
    public static function provideInvalidNames(): array
    {
        return [
            [''],
            [' '],
            ["\t"],
        ];
    }

    /**
     * Test invalid function names.
     * @param string $name
     * @throws IConnectionFailedException
     * @throws IIncompleteConfigException
     * @throws IInvalidArgumentException
     * @throws IUnknownFunctionException
     * @throws InvalidArgumentException
     * @dataProvider provideInvalidNames
     */
    public function testInvalidNames(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing or malformed SAP remote function name');
        AbstractFunctionInstance::create($name);
    }

    /**
     * @return array<int, array<int, array<int|string, bool|float|int|stdClass|string>>>
     */
    public static function provideMalformedNames(): array
    {
        return [
            [['FEeBlhUw' => 'ITfy2D12']],
            [[89420 => 'EaDvEX1g']],
            [[IFunction::JSON_NAME => 99209]],
            [[IFunction::JSON_NAME => 63.278]],
            [[IFunction::JSON_NAME => true]],
            [[IFunction::JSON_NAME => new stdClass()]],
        ];
    }

    /**
     * @param array<int|string, bool|float|int|stdClass|string> $array
     * @return void
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws InvalidArgumentException
     * @throws UnknownFunctionException
     * @dataProvider provideMalformedNames
     */
    public function testMalformedNames(array $array): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing tests\phpsap\classes\helper\AbstractFunctionInstance "name"');
        new AbstractFunctionInstance($array);
    }

    /**
     * Test setting and getting the SAP remote function name.
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSettingAndGettingName(): void
    {
        $fnc = AbstractFunctionInstance::create('BbkjmImI');
        static::assertInstanceOf(AbstractFunction::class, $fnc);
        static::assertSame('BbkjmImI', $fnc->getName());
    }

    /**
     * Test setting and getting different SAP connection configurations.
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws IConnectionFailedException
     * @throws IIncompleteConfigException
     * @throws IInvalidArgumentException
     * @throws IUnknownFunctionException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSettingAndGettingConfig(): void
    {
        $fnc = AbstractFunctionInstance::create('tjmecgsl', null, new ConfigTypeA());
        static::assertInstanceOf(AbstractFunction::class, $fnc);
        static::assertInstanceOf(ConfigTypeA::class, $fnc->getConfiguration());
        $fnc->setConfiguration(new ConfigTypeB());
        static::assertInstanceOf(ConfigTypeB::class, $fnc->getConfiguration());
    }

    /**
     * @return void
     * @throws IConnectionFailedException
     * @throws IIncompleteConfigException
     * @throws IInvalidArgumentException
     * @throws IUnknownFunctionException
     * @throws InvalidArgumentException
     */
    public function testMissingConfiguration(): void
    {
        $fnc = AbstractFunctionInstance::create('OGEQas0r');
        $this->expectException(IncompleteConfigException::class);
        $this->expectExceptionMessage('Missing configuration for');
        $fnc->getConfiguration();
    }

    /**
     * Test extracting and getting the SAP remote function API.
     * @throws ConnectionFailedException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws IncompleteConfigException
     * @throws InvalidArgumentException
     * @throws UnknownFunctionException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testExtractGetAndSetApi(): void
    {
        AbstractFunctionInstance::$fakeApi = [
            [
                IApiElement::JSON_NAME => 'vtQSToDd',
                IApiElement::JSON_TYPE => IValue::TYPE_INTEGER,
                IApiElement::JSON_DIRECTION => IApiElement::DIRECTION_OUTPUT,
                IApiElement::JSON_OPTIONAL => false
            ]
        ];
        $fnc1 = AbstractFunctionInstance::create('AcqwjdLj');
        static::assertInstanceOf(AbstractFunction::class, $fnc1);
        $api1 = $fnc1->getApi();
        static::assertInstanceOf(RemoteApi::class, $api1);
        $out1 = $api1->getOutputElements();
        static::assertCount(1, $out1);
        $value1 = array_pop($out1);
        static::assertInstanceOf(Value::class, $value1);
        static::assertSame('vtQSToDd', $value1->getName());
        static::assertSame(IValue::TYPE_INTEGER, $value1->getType());
        static::assertSame(IApiElement::DIRECTION_OUTPUT, $value1->getDirection());
        static::assertFalse($value1->isOptional());
        /**
         * Now change the response of the fake API and query the same function name
         * again.
         */
        AbstractFunctionInstance::$fakeApi = [
            [
                IApiElement::JSON_NAME => 'jugcqvMX',
                IApiElement::JSON_TYPE => IValue::TYPE_STRING,
                IApiElement::JSON_DIRECTION => IApiElement::DIRECTION_OUTPUT,
                IApiElement::JSON_OPTIONAL => true
            ]
        ];
        $fnc2 = AbstractFunctionInstance::create('AcqwjdLj');
        static::assertInstanceOf(AbstractFunction::class, $fnc2);
        $api2 = $fnc2->getApi();
        static::assertInstanceOf(RemoteApi::class, $api2);
        $out2 = $api2->getOutputElements();
        static::assertCount(1, $out2);
        $value2 = array_pop($out2);
        static::assertInstanceOf(Value::class, $value2);
        static::assertSame('vtQSToDd', $value2->getName());
        static::assertSame(IValue::TYPE_INTEGER, $value2->getType());
        static::assertSame(IApiElement::DIRECTION_OUTPUT, $value2->getDirection());
        static::assertFalse($value2->isOptional());
        /**
         * ... but, when extracting the actual API, we circumvent the cached API of
         * getApi().
         */
        $api3 = $fnc2->extractApi();
        static::assertInstanceOf(RemoteApi::class, $api3);
        $out3 = $api3->getOutputElements();
        static::assertCount(1, $out3);
        $value3 = array_pop($out3);
        static::assertInstanceOf(Value::class, $value3);
        static::assertSame('jugcqvMX', $value3->getName());
        static::assertSame(IValue::TYPE_STRING, $value3->getType());
        static::assertSame(IApiElement::DIRECTION_OUTPUT, $value3->getDirection());
        static::assertTrue($value3->isOptional());
        /**
         * Now we set a very different API.
         */
        $fnc2->setApi(new RemoteApi(
            [
                [
                    IApiElement::JSON_NAME => 'HTufsZQx',
                    IApiElement::JSON_TYPE => IValue::TYPE_STRING,
                    IApiElement::JSON_DIRECTION => IApiElement::DIRECTION_INPUT,
                    IApiElement::JSON_OPTIONAL => false
                ]
            ]
        ));
        $api4 = $fnc2->getApi();
        static::assertInstanceOf(RemoteApi::class, $api4);
        $input = $api4->getInputElements();
        static::assertCount(1, $input);
        $value4 = array_pop($input);
        static::assertInstanceOf(Value::class, $value4);
        static::assertSame('HTufsZQx', $value4->getName());
        static::assertSame(IValue::TYPE_STRING, $value4->getType());
        static::assertSame(IApiElement::DIRECTION_INPUT, $value4->getDirection());
        static::assertFalse($value4->isOptional());
    }

    /**
     * Test setting the API of a remote function via constructor.
     * @throws ConnectionFailedException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws IConnectionFailedException
     * @throws IIncompleteConfigException
     * @throws IInvalidArgumentException
     * @throws IUnknownFunctionException
     * @throws IncompleteConfigException
     * @throws InvalidArgumentException
     * @throws UnknownFunctionException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSetApiConstructor(): void
    {
        /**
         * Let the fake API differ from the one defined in the constructor.
         */
        AbstractFunctionInstance::$fakeApi = [[
            IApiElement::JSON_NAME => 'DYqDLpJJ',
            IApiElement::JSON_TYPE => IValue::TYPE_INTEGER,
            IApiElement::JSON_DIRECTION => IApiElement::DIRECTION_INPUT,
            IApiElement::JSON_OPTIONAL => true
        ]];
        $fnc = AbstractFunctionInstance::create('QYNlDnyf', null, null, new RemoteApi([
            [
                IApiElement::JSON_NAME => 'IdmGEBfI',
                IApiElement::JSON_TYPE => IValue::TYPE_STRING,
                IApiElement::JSON_DIRECTION => IApiElement::DIRECTION_INPUT,
                IApiElement::JSON_OPTIONAL => false
            ]
        ]));
        /**
         * Assert that not the fake API but the one given via the constructor is
         * returned.
         */
        static::assertInstanceOf(AbstractFunction::class, $fnc);
        $api = $fnc->getApi();
        static::assertInstanceOf(RemoteApi::class, $api);
        $api_inputs = $api->getInputElements();
        static::assertCount(1, $api_inputs);
        $api_input0 = array_pop($api_inputs);
        static::assertInstanceOf(Value::class, $api_input0);
        static::assertSame('IdmGEBfI', $api_input0->getName());
        static::assertSame(IValue::TYPE_STRING, $api_input0->getType());
        static::assertSame(IApiElement::DIRECTION_INPUT, $api_input0->getDirection());
        static::assertFalse($api_input0->isOptional());
    }

    /**
     * Test set and get parameters.
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws IConnectionFailedException
     * @throws IIncompleteConfigException
     * @throws IInvalidArgumentException
     * @throws IUnknownFunctionException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSetAndGetParameters(): void
    {
        AbstractFunctionInstance::$fakeApi = [
            [
                IApiElement::JSON_NAME => 'OkUxzPbS',
                IApiElement::JSON_TYPE => IValue::TYPE_STRING,
                IApiElement::JSON_DIRECTION => IApiElement::DIRECTION_INPUT,
                IApiElement::JSON_OPTIONAL => false
            ],
            [
                IApiElement::JSON_NAME => 'ePmpwEHW',
                IApiElement::JSON_TYPE => IStruct::TYPE_STRUCT,
                IApiElement::JSON_DIRECTION => IApiElement::DIRECTION_INPUT,
                IApiElement::JSON_OPTIONAL => false,
                IStruct::JSON_MEMBERS => [
                    [
                        IMember::JSON_NAME => 'llnwSfRS',
                        IMember::JSON_TYPE => IMember::TYPE_STRING
                    ],
                    [
                        IMember::JSON_NAME => 'aqCcYeax',
                        IMember::JSON_TYPE => IMember::TYPE_INTEGER
                    ]
                ]
            ],
            [
                IApiElement::JSON_NAME => 'gksKixRv',
                IApiElement::JSON_TYPE => ITable::TYPE_TABLE,
                IApiElement::JSON_DIRECTION => ITable::DIRECTION_TABLE,
                IApiElement::JSON_OPTIONAL => false,
                ITable::JSON_MEMBERS => [
                    [
                        IMember::JSON_NAME => 'pLDXUMoT',
                        IMember::JSON_TYPE => IMember::TYPE_STRING
                    ],
                    [
                        IMember::JSON_NAME => 'rpJNsIjC',
                        IMember::JSON_TYPE => IMember::TYPE_INTEGER
                    ]
                ]
            ]
        ];
        $fnc = AbstractFunctionInstance::create('XkKxjVCh', [
            'OkUxzPbS' => 'AtouLfAE',
            'ePmpwEHW' => [
                'llnwSfRS' => 'tzmvidMm',
                'aqCcYeax' => 64430
            ],
            'gksKixRv' => [
                [
                    'pLDXUMoT' => 'SPqbjvnb',
                    'rpJNsIjC' => 27370
                ],
                [
                    'pLDXUMoT' => 'JpFtgGQA',
                    'rpJNsIjC' => 28939
                ]
            ]
        ]);
        static::assertInstanceOf(AbstractFunction::class, $fnc);
        $fnc->setParam('OkUxzPbS', 'FVnhTAoQ');
        static::assertSame('FVnhTAoQ', $fnc->getParam('OkUxzPbS'));

        /** @var array<string, mixed> $params */
        $params = $fnc->getParams();
        static::assertArrayHasKey('OkUxzPbS', $params);
        static::assertIsString($params['OkUxzPbS']);
        static::assertSame('FVnhTAoQ', $params['OkUxzPbS']);

        static::assertArrayHasKey('ePmpwEHW', $params);
        /** @var array<string, mixed> $ePmpwEHW */
        $ePmpwEHW = $params['ePmpwEHW'];
        static::assertArrayHasKey('llnwSfRS', $ePmpwEHW);
        static::assertIsString($ePmpwEHW['llnwSfRS']);
        static::assertSame('tzmvidMm', $ePmpwEHW['llnwSfRS']);

        static::assertArrayHasKey('aqCcYeax', $ePmpwEHW);
        static::assertIsInt($ePmpwEHW['aqCcYeax']);
        static::assertSame(64430, $ePmpwEHW['aqCcYeax']);

        static::assertArrayHasKey('gksKixRv', $params);
        /** @var array<int, array<string, mixed>> $gksKixRv */
        $gksKixRv = $params['gksKixRv'];
        static::assertCount(2, $gksKixRv);

        static::assertArrayHasKey(0, $gksKixRv);
        /** @var array<string, mixed> $gksKixRv0 */
        $gksKixRv0 = $gksKixRv[0];
        static::assertArrayHasKey('pLDXUMoT', $gksKixRv0);
        static::assertIsString($gksKixRv0['pLDXUMoT']);
        static::assertSame('SPqbjvnb', $gksKixRv0['pLDXUMoT']);

        static::assertArrayHasKey('rpJNsIjC', $gksKixRv0);
        static::assertIsInt($gksKixRv0['rpJNsIjC']);
        static::assertSame(27370, $gksKixRv0['rpJNsIjC']);

        static::assertArrayHasKey(1, $gksKixRv);
        /** @var array<string, mixed> $gksKixRv1 */
        $gksKixRv1 = $gksKixRv[1];
        static::assertArrayHasKey('pLDXUMoT', $gksKixRv1);
        static::assertIsString($gksKixRv1['pLDXUMoT']);
        static::assertSame('JpFtgGQA', $gksKixRv1['pLDXUMoT']);

        static::assertArrayHasKey('rpJNsIjC', $gksKixRv1);
        static::assertIsInt($gksKixRv1['rpJNsIjC']);
        static::assertSame(28939, $gksKixRv1['rpJNsIjC']);

        /**
         * Now reset all parameters.
         */
        $fnc->resetParams();
        static::assertSame([], $fnc->getParams());
    }

    /**
     * Test JSON serialization.
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws IConnectionFailedException
     * @throws IIncompleteConfigException
     * @throws IInvalidArgumentException
     * @throws IUnknownFunctionException
     */
    public function testJsonSerialization(): void
    {
        AbstractFunctionInstance::$fakeApi = [
            [
                IApiElement::JSON_NAME => 'UOvOMBva',
                IApiElement::JSON_TYPE => IValue::TYPE_STRING,
                IApiElement::JSON_DIRECTION => IApiElement::DIRECTION_INPUT,
                IApiElement::JSON_OPTIONAL => false
            ]
        ];
        $fnc = AbstractFunctionInstance::create('GUGtjHBL', ['UOvOMBva' => 'IGxIqMvU']);
        static::assertInstanceOf(AbstractFunction::class, $fnc);
        $json = json_encode($fnc);
        static::assertIsString($json);
        $expected = '{"name":"GUGtjHBL",'
                    . '"api":[{"type":"string","name":"UOvOMBva","direction":"input","optional":false}],'
                    . '"params":{"UOvOMBva":"IGxIqMvU"}}';
        static::assertSame($expected, $json);
    }

    /**
     * Test JSON deserialization.
     * @throws IncompleteConfigException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ConnectionFailedException
     * @throws UnknownFunctionException
     */
    public function testJsonDeserialization(): void
    {
        AbstractFunctionInstance::$fakeApi = [
            [
                IApiElement::JSON_NAME => 'dvPoAdYG',
                IApiElement::JSON_TYPE => IValue::TYPE_STRING,
                IApiElement::JSON_DIRECTION => IApiElement::DIRECTION_INPUT,
                IApiElement::JSON_OPTIONAL => false
            ]
        ];
        $json = '{"name":"MLPmcnyT",'
                . '"api":[{"type":"string","name":"dvPoAdYG","direction":"input","optional":false}],'
                . '"params":{"dvPoAdYG":"LHpcxfLz"}}';
        $fnc = AbstractFunctionInstance::jsonDecode($json);
        static::assertInstanceOf(AbstractFunction::class, $fnc);

        $params = $fnc->getParams();
        static::assertArrayHasKey('dvPoAdYG', $params);
        static::assertSame('LHpcxfLz', $params['dvPoAdYG']);

        $api = $fnc->getApi();
        static::assertInstanceOf(RemoteApi::class, $api);

        $input_values = $api->getInputElements();
        static::assertCount(1, $input_values);

        $input_value0 = array_pop($input_values);
        static::assertInstanceOf(Value::class, $input_value0);
        static::assertSame('dvPoAdYG', $input_value0->getName());
        static::assertSame(IApiElement::DIRECTION_INPUT, $input_value0->getDirection());
        static::assertSame(IValue::TYPE_STRING, $input_value0->getType());
        static::assertFalse($input_value0->isOptional());
    }

    /**
     * Data provider for invalid JSON.
     * @return array<int, array<int, string>>
     */
    public static function provideInvalidJson(): array
    {
        return [
            [
                '{"name":"rgVjZtqB","params":{"JBBIPySA":"7897467303"}}'
            ],
            [
                '{"name":"rgVjZtqB","api":[{"type":"string","name":"rgVjZtqB","direction":"input","optional":false}],'
            ],
            [
                '"api":[{"type":"string","name":"rgVjZtqB","direction":"input","optional":false}],'
                . '"params":{"BZBl7u6w":"CLsVlAje"}}'
            ],
            [
                '"api":[{"type":"string","name":"rgVjZtqB","direction":"input","optional":false}],'
                . '"params":{"DbF6y6oE":"CLsVlAje"}}'
            ],
            ['7072.8'],
            ['true']
        ];
    }

    /**
     * Test invalid JSON
     * @param string $json
     * @throws IConnectionFailedException
     * @throws IIncompleteConfigException
     * @throws IInvalidArgumentException
     * @throws IUnknownFunctionException
     * @throws InvalidArgumentException
     * @dataProvider provideInvalidJson
     */
    public function testInvalidJson(string $json): void
    {
        AbstractFunctionInstance::$fakeApi = [
            [
                IApiElement::JSON_NAME => 'JBBIPySA',
                IApiElement::JSON_TYPE => IValue::TYPE_STRING,
                IApiElement::JSON_DIRECTION => IApiElement::DIRECTION_INPUT,
                IApiElement::JSON_OPTIONAL => false
            ]
        ];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON: Expected JSON encoded');
        AbstractFunctionInstance::jsonDecode($json);
    }
}
