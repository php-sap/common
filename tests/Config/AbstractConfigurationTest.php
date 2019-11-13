<?php

namespace tests\phpsap\classes\Config;

use phpsap\classes\Config\AbstractConfiguration;
use phpsap\classes\Config\ConfigCommon;
use phpsap\classes\Config\ConfigTypeA;
use phpsap\classes\Config\ConfigTypeB;
use phpsap\interfaces\Config\IConfiguration;
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
class AbstractConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the inheritance of the AbstractConfiguration class.
     */
    public function testInheritance()
    {
        AbstractConfigurationInstance::$validConfigKeys = [];
        $config = new AbstractConfigurationInstance();
        static::assertInstanceOf(\JsonSerializable::class, $config);
        static::assertInstanceOf(IConfiguration::class, $config);
        static::assertInstanceOf(AbstractConfiguration::class, $config);
    }

    /**
     * Test successful set() get() has() and remove().
     */
    public function testSuccessfulSetGetHasAndRemove()
    {
        AbstractConfigurationInstance::$validConfigKeys = [
            'vQVWBaPY', 'PpTzacjc'
        ];
        $config = new AbstractConfigurationInstance();
        $config
            ->set('vQVWBaPY', 'AYP2RY1vaS')
            ->set('PpTzacjc', 9167);
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
     * Data provider for invalid key names.
     */
    public static function provideInvalidKeyNames()
    {
        return [
            [''],
            [9217],
            [783.9],
            [true],
            [false],
            [null],
            [['8hvws0dCVF']],
            [new \stdClass()]
        ];
    }

    /**
     * Test invalid key names for has().
     * @param mixed $key
     * @dataProvider provideInvalidKeyNames
     * @expectedException LogicException
     * @expectedExceptionMessage Expected configuration key to be a string value.
     */
    public function testInvalidKeyNamesForHas($key)
    {
        (new AbstractConfigurationInstance())->has($key);
    }

    /**
     * Test invalid key names for has().
     * @param mixed $key
     * @dataProvider provideInvalidKeyNames
     * @expectedException LogicException
     * @expectedExceptionMessage Expected configuration key to be a string value.
     */
    public function testInvalidKeyNamesForSet($key)
    {
        (new AbstractConfigurationInstance())->set($key, 'D1bi58rP32');
    }

    /**
     * Test unknown configuration key for has().
     * @expectedException \LogicException
     * @expectedExceptionMessage Unknown configuration key 'gpOZijhM'!
     */
    public function testUnknownConfigurationKeyForHas()
    {
        AbstractConfigurationInstance::$validConfigKeys = ['ckyVAAPl'];
        (new AbstractConfigurationInstance())->has('gpOZijhM');
    }

    /**
     * Test unknown configuration key for set().
     * @expectedException \LogicException
     * @expectedExceptionMessage Unknown configuration key 'JDsUJLrq'!
     */
    public function testUnknownConfigurationKeyForSet()
    {
        AbstractConfigurationInstance::$validConfigKeys = ['asemoqTU'];
        (new AbstractConfigurationInstance())->set('JDsUJLrq', '84VUPgAS2i');
    }

    /**
     * Test get()ting an unset configuration key.
     * @expectedException \phpsap\exceptions\ConfigKeyNotFoundException
     * @expectedExceptionMessage Configuration key 'SXOtJQme' not found!
     */
    public function testGettingUnsetConfigurationKey()
    {
        AbstractConfigurationInstance::$validConfigKeys = ['SXOtJQme'];
        (new AbstractConfigurationInstance())->get('SXOtJQme');
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
            [new \stdClass()]
        ];
    }

    /**
     * Test invalid value for set().
     * @param mixed $value
     * @dataProvider provideInvalidValueForSet
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected configuration value for 'FsgNGucN' to either be a string or integer value!
     */
    public function testInvalidValueForSet($value)
    {
        AbstractConfigurationInstance::$validConfigKeys = ['FsgNGucN'];
        (new AbstractConfigurationInstance())->set('FsgNGucN', $value);
    }

    /**
     * Data provider of valid configuration parameters for the constructor.
     * @return array
     */
    public static function provideValidConfigurationForConstructor()
    {
        $conf = new \stdClass();
        $conf->zadgcjmt = 'wntQeayy41';
        return [
            [$conf],
            [json_encode($conf)],
            [json_decode(json_encode($conf), true)]
        ];
    }

    /**
     * Test valid configuration parameters for the constructor.
     * @param string|array|\stdClass $config
     * @dataProvider provideValidConfigurationForConstructor
     */
    public function testValidConfigurationForConstructor($config)
    {
        AbstractConfigurationInstance::$validConfigKeys = ['zadgcjmt'];
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
     * @throws \Exception
     */
    public static function provideInvalidConfigurationForConstructor()
    {
        return [
            [5126],
            [97.65],
            [true],
            [false],
            [new \DateTime()]
        ];
    }

    /**
     * Test valid configuration parameters for the constructor.
     * @param mixed $config
     * @dataProvider provideInvalidConfigurationForConstructor
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected configuration to be a JSON encoded array!
     */
    public function testInvalidConfigurationForConstructor($config)
    {
        new AbstractConfigurationInstance($config);
    }

    /**
     * Data provider of ConfigTypeA configuration for jsonDecode().
     * @return array
     */
    public static function provideJsonDecodeConfigTypeA()
    {
        return [
            [[ConfigTypeA::JSON_ASHOST => 'ItulITyML1'], '{"ashost":"ItulITyML1"}'],
            [[ConfigTypeA::JSON_SYSNR => '5345'], '{"sysnr":"5345"}'],
            [[ConfigTypeA::JSON_GWHOST => '6sqPJLVVgS'], '{"gwhost":"6sqPJLVVgS"}'],
            [[ConfigTypeA::JSON_GWSERV => 'pzkPI1ZV7f'], '{"gwserv":"pzkPI1ZV7f"}']
        ];
    }

    /**
     * Test jsonDecode() for configuration type A.
     * @param array $array
     * @param string $json
     * @dataProvider provideJsonDecodeConfigTypeA
     */
    public function testJsonDecodeConfigTypeA($array, $json)
    {
        $config = AbstractConfiguration::jsonDecode($array);
        static::assertInstanceOf(ConfigTypeA::class, $config);
        static::assertJsonStringEqualsJsonString($json, json_encode($config));
    }

    /**
     * Data provider of ConfigTypeB configuration for jsonDecode().
     * @return array
     */
    public static function provideJsonDecodeConfigTypeB()
    {
        return [
            [[ConfigTypeB::JSON_MSHOST => '4htV2O3BMH'], '{"mshost":"4htV2O3BMH"}'],
            [[ConfigTypeB::JSON_R3NAME => 'XmJsmqU3ua'], '{"r3name":"XmJsmqU3ua"}'],
            [[ConfigTypeB::JSON_GROUP => 'Tczw3KTagh'], '{"group":"Tczw3KTagh"}']
        ];
    }

    /**
     * Test jsonDecode() for configuration type B.
     * @param array $array
     * @param string $json
     * @dataProvider provideJsonDecodeConfigTypeB
     */
    public function testJsonDecodeConfigTypeB($array, $json)
    {
        $config = AbstractConfiguration::jsonDecode($array);
        static::assertInstanceOf(ConfigTypeB::class, $config);
        static::assertJsonStringEqualsJsonString($json, json_encode($config));
    }

    /**
     * Data provider for invalid jsonDecode() configuration.
     * @return array
     */
    public static function provideInvalidJsonDecodeConfiguration()
    {
        return [
            [[ConfigCommon::JSON_CLIENT => '2107']],
            [[ConfigCommon::JSON_CODEPAGE => '6692']],
            [[ConfigCommon::JSON_LANG => 'EN']],
            [[ConfigCommon::JSON_SAPROUTER => '/H/UHWGvuoGxO/S/6770/H/']],
            [['ifmOmNbx' => 6862]]
        ];
    }

    /**
     * @param array $array
     * @dataProvider provideInvalidJsonDecodeConfiguration
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cannot automatically determine the configuration type from the given configuration keys!
     */
    public function testInvalidJsonDecodeConfiguration($array)
    {
        AbstractConfiguration::jsonDecode($array);
    }
}
