<?php

namespace tests\phpsap\classes\Config;

use Exception;
use phpsap\exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use phpsap\classes\Util\JsonSerializable;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Exception;
use stdClass;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\classes\Config\AbstractConfiguration;
use phpsap\classes\Config\ConfigCommon;
use phpsap\classes\Config\ConfigTypeA;
use phpsap\classes\Config\ConfigTypeB;
use tests\phpsap\classes\helper\AbstractConfigurationInstance;

/**
 * Class tests\phpsap\classes\Config\AbstractConfigurationTest
 *
 * Test AbstractConfiguration class via the proxy class AbstractConfigurationInstance
 * where all protected methods are made public.
 *
 * @package tests\phpsap\classes\Config
 * @author  Gregor J.
 * @license MIT
 */
class AbstractConfigurationTest extends TestCase
{
    /**
     * Test the inheritance of the AbstractConfiguration class.
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidArgumentException
     */
    public function testInheritance()
    {
        AbstractConfigurationInstance::$allowedKeys = [];
        $config = new AbstractConfigurationInstance();
        static::assertInstanceOf(JsonSerializable::class, $config);
        static::assertInstanceOf(IConfiguration::class, $config);
        static::assertInstanceOf(AbstractConfiguration::class, $config);
    }

    /**
     * Test successful set() get() has() and remove().
     * @throws InvalidArgumentException
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public function testSuccessfulSetGetHasAndRemove()
    {
        AbstractConfigurationInstance::$allowedKeys = [
            'vQVWBaPY', 'PpTzacjc'
        ];
        $config = new AbstractConfigurationInstance();
        $config->set('vQVWBaPY', 'AYP2RY1vaS');
        $config->set('PpTzacjc', 9167);
        static::assertTrue($config->has('vQVWBaPY'));
        static::assertSame('AYP2RY1vaS', $config->get('vQVWBaPY'));
        static::assertTrue($config->has('PpTzacjc'));
        static::assertSame(9167, $config->get('PpTzacjc'));
        static::assertJsonStringEqualsJsonString(
            '{"vQVWBaPY":"AYP2RY1vaS","PpTzacjc":9167}',
            json_encode($config)
        );
        $config->remove('vQVWBaPY');
        static::assertFalse($config->has('vQVWBaPY'));
        $config->set('PpTzacjc', null);
        static::assertFalse($config->has('PpTzacjc'));
        static::assertJsonStringEqualsJsonString(
            '{}',
            json_encode($config)
        );
    }

    /**
     * Test unknown configuration key for set().
     */
    public function testUnknownConfigurationKeyForSet()
    {
        AbstractConfigurationInstance::$allowedKeys = ['asemoqTU'];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown key \'JDsUJLrq\'!');
        (new AbstractConfigurationInstance())->set('JDsUJLrq', '84VUPgAS2i');
    }

    /**
     * Test get()ting an unset configuration key.
     */
    public function testGettingUnsetConfigurationKey()
    {
        AbstractConfigurationInstance::$allowedKeys = ['SXOtJQme'];
        static::assertNull((new AbstractConfigurationInstance())->get('SXOtJQme'));
    }

    /**
     * Data provider for invalid values for set().
     * @return array
     */
    public static function provideInvalidValueForSet()
    {
        return [
            [1.38],
            [true],
            [false],
            [['FsgNGucN' => 7133]],
            [new stdClass()]
        ];
    }

    /**
     * Test invalid value for set().
     * @param mixed $value
     * @dataProvider provideInvalidValueForSet
     */
    public function testInvalidValueForSet($value)
    {
        AbstractConfigurationInstance::$allowedKeys = ['FsgNGucN'];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value! Expected a simple value (\'integer\', \'string\'), but got');
        (new AbstractConfigurationInstance())->set('FsgNGucN', $value);
    }

    /**
     * Data provider of valid configuration parameters for the constructor.
     * @return array
     */
    public static function provideValidConfigurationForConstructor()
    {
        $conf = new stdClass();
        $conf->zadgcjmt = 'wntQeayy41';
        return [
            [$conf],
            [json_encode($conf)],
            [json_decode(json_encode($conf), true)]
        ];
    }

    /**
     * Test valid configuration parameters for the constructor.
     * @param string|array|stdClass $config
     * @dataProvider provideValidConfigurationForConstructor
     * @throws InvalidArgumentException
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public function testValidConfigurationForConstructor($config)
    {
        AbstractConfigurationInstance::$allowedKeys = ['zadgcjmt'];
        $conf = new AbstractConfigurationInstance($config);
        static::assertTrue($conf->has('zadgcjmt'));
        static::assertSame('wntQeayy41', $conf->get('zadgcjmt'));
        static::assertJsonStringEqualsJsonString(
            '{"zadgcjmt":"wntQeayy41"}',
            json_encode($conf)
        );
    }

    /**
     * Data provider of valid configuration parameters for the constructor.
     * @return array
     * @throws Exception
     */
    public static function provideInvalidConfigurationForConstructor()
    {
        return [
            [5126],
            [97.65],
            [true],
            [false]
        ];
    }

    /**
     * Test valid configuration parameters for the constructor.
     * @param mixed $config
     * @dataProvider provideInvalidConfigurationForConstructor
     */
    public function testInvalidConfigurationForConstructor($config)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid JSON object! '
            . 'Expected tests\\phpsap\\classes\\helper\\AbstractConfigurationInstance JSON object or array!'
        );
        new AbstractConfigurationInstance($config);
    }

    /**
     * Data provider of ConfigTypeA configuration for jsonDecode().
     * @return array
     */
    public static function provideJsonDecodeConfigTypeA()
    {
        return [
            [[ConfigTypeA::JSON_ASHOST => 'ItulITyML1'], '{"' . ConfigTypeA::JSON_ASHOST . '":"ItulITyML1"}'],
            [[ConfigTypeA::JSON_SYSNR => '5345'], '{"' . ConfigTypeA::JSON_SYSNR . '":"5345"}'],
            [[ConfigTypeA::JSON_GWHOST => '6sqPJLVVgS'], '{"' . ConfigTypeA::JSON_GWHOST . '":"6sqPJLVVgS"}'],
            [[ConfigTypeA::JSON_GWSERV => 'pzkPI1ZV7f'], '{"' . ConfigTypeA::JSON_GWSERV . '":"pzkPI1ZV7f"}']
        ];
    }

    /**
     * Test jsonDecode() for configuration type A.
     * @param array  $array
     * @param string $json
     * @dataProvider provideJsonDecodeConfigTypeA
     * @throws InvalidArgumentException
     * @throws PHPUnit_Framework_Exception
     */
    public function testJsonDecodeConfigTypeA($array, $json)
    {
        $config = AbstractConfiguration::jsonDecode($json);
        static::assertInstanceOf(ConfigTypeA::class, $config);
        static::assertSame($array, $config->toArray());
    }

    /**
     * Data provider of ConfigTypeB configuration for jsonDecode().
     * @return array
     */
    public static function provideJsonDecodeConfigTypeB()
    {
        return [
            [[ConfigTypeB::JSON_MSHOST => '4htV2O3BMH'], '{"' . ConfigTypeB::JSON_MSHOST . '":"4htV2O3BMH"}'],
            [[ConfigTypeB::JSON_R3NAME => 'XmJsmqU3ua'], '{"' . ConfigTypeB::JSON_R3NAME . '":"XmJsmqU3ua"}'],
            [[ConfigTypeB::JSON_GROUP => 'Tczw3KTagh'], '{"' . ConfigTypeB::JSON_GROUP . '":"Tczw3KTagh"}']
        ];
    }

    /**
     * Test jsonDecode() for configuration type B.
     * @param array  $array
     * @param string $json
     * @dataProvider provideJsonDecodeConfigTypeB
     * @throws InvalidArgumentException
     * @throws PHPUnit_Framework_Exception
     */
    public function testJsonDecodeConfigTypeB($array, $json)
    {
        $config = AbstractConfiguration::jsonDecode($json);
        static::assertInstanceOf(ConfigTypeB::class, $config);
        static::assertSame($array, $config->toArray());
    }

    /**
     * Data provider of non-specific configuration JSON strings.
     * @return array[]
     */
    public static function provideNonSpecificJson()
    {
        return [
            ['{}'],
            ['{"' . ConfigCommon::JSON_CLIENT . '":"001"}'],
            ['{"' . ConfigCommon::JSON_USER . '":"username"}'],
            ['{"' . ConfigCommon::JSON_PASSWD . '":"password"}']
        ];
    }

    /**
     * Test decoding a JSON that is not type specific.
     * @param string $json
     * @dataProvider             provideNonSpecificJson
     * @throws InvalidArgumentException
     */
    public function testNonSpecificJson($json)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot automatically determine the configuration type');
        AbstractConfiguration::jsonDecode($json);
    }
}
