<?php

/** @noinspection PhpMethodNamingConventionInspection */

declare(strict_types=1);

namespace tests\phpsap\classes\Config;

use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Config\IConfigCommon;
use phpsap\interfaces\Config\IConfigTypeA;
use phpsap\interfaces\Config\IConfigTypeB;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use phpsap\classes\Util\JsonSerializable;
use stdClass;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\classes\Config\AbstractConfiguration;
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
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInheritance(): void
    {
        AbstractConfigurationInstance::$allowedKeys = [];
        $config = new AbstractConfigurationInstance();
        static::assertInstanceOf(JsonSerializable::class, $config);
        static::assertInstanceOf(IConfiguration::class, $config);
        static::assertInstanceOf(AbstractConfiguration::class, $config);
    }

    /**
     * Test successful set() get() has() and remove().
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSuccessfulSetGetHasRemove(): void
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
    public function testUnknownConfigurationKeyForSet(): void
    {
        AbstractConfigurationInstance::$allowedKeys = ['asemoqTU'];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown key \'JDsUJLrq\'!');
        (new AbstractConfigurationInstance())->set('JDsUJLrq', '84VUPgAS2i');
    }

    /**
     * Test get()ting an unset configuration key.
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGettingUnsetConfigurationKey(): void
    {
        AbstractConfigurationInstance::$allowedKeys = ['SXOtJQme'];
        static::assertNull((new AbstractConfigurationInstance())->get('SXOtJQme'));
    }

    /**
     * Data provider of valid configuration parameters for the constructor.
     * @return array<int, array<int, string|array|stdClass>>
     */
    public static function provideValidConfigurationForConstructor(): array
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
     * @param array|string|stdClass $config
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideValidConfigurationForConstructor
     */
    public function testValidConfigurationForConstructor(array|string|stdClass $config): void
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
     * Data provider of ConfigTypeA configuration for jsonDecode().
     * @return array<int, array<int, array|string>>
     */
    public static function provideJsonDecodeConfigTypeA(): array
    {
        return [
            [[IConfigTypeA::JSON_ASHOST => 'ItulITyML1'], '{"' . IConfigTypeA::JSON_ASHOST . '":"ItulITyML1"}'],
            [[IConfigTypeA::JSON_SYSNR => '5345'], '{"' . IConfigTypeA::JSON_SYSNR . '":"5345"}'],
            [[IConfigTypeA::JSON_GWHOST => '6sqPJLVVgS'], '{"' . IConfigTypeA::JSON_GWHOST . '":"6sqPJLVVgS"}'],
            [[IConfigTypeA::JSON_GWSERV => 'pzkPI1ZV7f'], '{"' . IConfigTypeA::JSON_GWSERV . '":"pzkPI1ZV7f"}']
        ];
    }

    /**
     * Test jsonDecode() for configuration type A.
     * @param array $array
     * @param string $json
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideJsonDecodeConfigTypeA
     */
    public function testJsonDecodeConfigTypeA(array $array, string $json): void
    {
        $config = AbstractConfiguration::jsonDecode($json);
        static::assertInstanceOf(ConfigTypeA::class, $config);
        static::assertSame($array, $config->toArray());
    }

    /**
     * Data provider of ConfigTypeB configuration for jsonDecode().
     * @return array<int, array<int, array|string>>
     */
    public static function provideJsonDecodeConfigTypeB(): array
    {
        return [
            [[IConfigTypeB::JSON_MSHOST => '4htV2O3BMH'], '{"' . IConfigTypeB::JSON_MSHOST . '":"4htV2O3BMH"}'],
            [[IConfigTypeB::JSON_R3NAME => 'XmJsmqU3ua'], '{"' . IConfigTypeB::JSON_R3NAME . '":"XmJsmqU3ua"}'],
            [[IConfigTypeB::JSON_GROUP => 'Tczw3KTagh'], '{"' . IConfigTypeB::JSON_GROUP . '":"Tczw3KTagh"}']
        ];
    }

    /**
     * Test jsonDecode() for configuration type B.
     * @param array $array
     * @param string $json
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideJsonDecodeConfigTypeB
     */
    public function testJsonDecodeConfigTypeB(array $array, string $json): void
    {
        $config = AbstractConfiguration::jsonDecode($json);
        static::assertInstanceOf(ConfigTypeB::class, $config);
        static::assertSame($array, $config->toArray());
    }

    /**
     * Data provider of non-specific configuration JSON strings.
     * @return array<int, array<int, string>>
     */
    public static function provideNonSpecificJson(): array
    {
        return [
            ['{}'],
            ['{"' . IConfigCommon::JSON_CLIENT . '":"001"}'],
            ['{"' . IConfigCommon::JSON_USER . '":"username"}'],
            ['{"' . IConfigCommon::JSON_PASSWD . '":"password"}']
        ];
    }

    /**
     * Test decoding a JSON that is not type specific.
     * @param string $json
     * @dataProvider provideNonSpecificJson
     * @throws InvalidArgumentException
     */
    public function testNonSpecificJson(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot automatically determine the configuration type');
        AbstractConfiguration::jsonDecode($json);
    }
}
