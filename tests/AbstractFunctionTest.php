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
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInheritance(): void
    {
        $fnc = new AbstractFunctionInstance('QifKTqzu');
        static::assertInstanceOf(IFunction::class, $fnc);
        static::assertInstanceOf(JsonSerializable::class, $fnc);
        static::assertInstanceOf(AbstractFunction::class, $fnc);
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
        new AbstractFunctionInstance($name);
    }

    /**
     * Test setting and getting the SAP remote function name.
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSettingAndGettingName(): void
    {
        $fnc = new AbstractFunctionInstance('BbkjmImI');
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
    public function testSettingAndGettingConfiguration(): void
    {
        $fnc = new AbstractFunctionInstance('tjmecgsl', null, new ConfigTypeA());
        static::assertInstanceOf(AbstractFunction::class, $fnc);
        static::assertInstanceOf(ConfigTypeA::class, $fnc->getConfiguration());
        $fnc->setConfiguration(new ConfigTypeB());
        static::assertInstanceOf(ConfigTypeB::class, $fnc->getConfiguration());
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
        $fnc1 = new AbstractFunctionInstance('AcqwjdLj');
        static::assertInstanceOf(AbstractFunction::class, $fnc1);
        $api1 = $fnc1->getApi();
        static::assertInstanceOf(RemoteApi::class, $api1);
        $out1 = $api1->getOutputElements();
        static::assertIsArray($out1);
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
        $fnc2 = new AbstractFunctionInstance('AcqwjdLj');
        static::assertInstanceOf(AbstractFunction::class, $fnc2);
        $api2 = $fnc2->getApi();
        static::assertInstanceOf(RemoteApi::class, $api2);
        $out2 = $api2->getOutputElements();
        static::assertIsArray($out2);
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
        static::assertIsArray($out3);
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
        static::assertIsArray($input);
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
        $fnc = new AbstractFunctionInstance('QYNlDnyf', null, null, new RemoteApi([
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
        static::assertIsArray($api_inputs);
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
        $fnc = new AbstractFunctionInstance('XkKxjVCh', [
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

        $params = $fnc->getParams();
        static::assertIsArray($params);

        static::assertArrayHasKey('OkUxzPbS', $params);
        static::assertIsString($params['OkUxzPbS']);
        static::assertSame('FVnhTAoQ', $params['OkUxzPbS']);

        static::assertArrayHasKey('ePmpwEHW', $params);
        static::assertIsArray($params['ePmpwEHW']);

        static::assertArrayHasKey('llnwSfRS', $params['ePmpwEHW']);
        static::assertIsString($params['ePmpwEHW']['llnwSfRS']);
        static::assertSame('tzmvidMm', $params['ePmpwEHW']['llnwSfRS']);

        static::assertArrayHasKey('aqCcYeax', $params['ePmpwEHW']);
        static::assertIsInt($params['ePmpwEHW']['aqCcYeax']);
        static::assertSame(64430, $params['ePmpwEHW']['aqCcYeax']);

        static::assertArrayHasKey('gksKixRv', $params);
        static::assertIsArray($params['gksKixRv']);
        static::assertCount(2, $params['gksKixRv']);

        static::assertArrayHasKey(0, $params['gksKixRv']);
        static::assertIsArray($params['gksKixRv'][0]);

        static::assertArrayHasKey('pLDXUMoT', $params['gksKixRv'][0]);
        static::assertIsString($params['gksKixRv'][0]['pLDXUMoT']);
        static::assertSame('SPqbjvnb', $params['gksKixRv'][0]['pLDXUMoT']);

        static::assertArrayHasKey('rpJNsIjC', $params['gksKixRv'][0]);
        static::assertIsInt($params['gksKixRv'][0]['rpJNsIjC']);
        static::assertSame(27370, $params['gksKixRv'][0]['rpJNsIjC']);

        static::assertArrayHasKey(1, $params['gksKixRv']);
        static::assertIsArray($params['gksKixRv'][1]);

        static::assertArrayHasKey('pLDXUMoT', $params['gksKixRv'][1]);
        static::assertIsString($params['gksKixRv'][1]['pLDXUMoT']);
        static::assertSame('JpFtgGQA', $params['gksKixRv'][1]['pLDXUMoT']);

        static::assertArrayHasKey('rpJNsIjC', $params['gksKixRv'][1]);
        static::assertIsInt($params['gksKixRv'][1]['rpJNsIjC']);
        static::assertSame(28939, $params['gksKixRv'][1]['rpJNsIjC']);

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
        $fnc = new AbstractFunctionInstance('GUGtjHBL', ['UOvOMBva' => 'IGxIqMvU']);
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
        static::assertIsArray($params);
        static::assertArrayHasKey('dvPoAdYG', $params);
        static::assertSame('LHpcxfLz', $params['dvPoAdYG']);

        $api = $fnc->getApi();
        static::assertInstanceOf(RemoteApi::class, $api);

        $input_values = $api->getInputElements();
        static::assertIsArray($input_values);
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
                '{"name":"rgVjZtqB",'
                . '"params":{"JBBIPySA":"7897467303"}}'
            ],
            [
                '{"name":"rgVjZtqB",'
                . '"api":[{"type":"string","name":"rgVjZtqB","direction":"input","optional":false}],'
            ],
            [
                '"api":[{"type":"string","name":"rgVjZtqB","direction":"input","optional":false}],'
                . '"params":{"dvPoAdYG":"CLsVlAje"}}'
            ],
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
        $this->expectExceptionMessage('Invalid JSON!');
        AbstractFunctionInstance::jsonDecode($json);
    }
}
