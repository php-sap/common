<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Config\Traits;

use phpsap\classes\Api\Member;
use phpsap\classes\Config\ConfigTypeA;
use phpsap\classes\Config\ConfigTypeB;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Config\IConfigTypeA;
use phpsap\interfaces\Config\IConfigTypeB;
use phpsap\interfaces\Config\IConfiguration;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonDecodeTraitTest
 */
class JsonDecodeTraitTest extends TestCase
{
    /**
     * Data provider of ConfigTypeA configuration for jsonDecode().
     * @return array<int, array<int, array<string, string|int>|string>>
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
     * @param array<string, string|int> $array
     * @param string $json
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideJsonDecodeConfigTypeA
     */
    public function testJsonDecodeConfigTypeA(array $array, string $json): void
    {
        $config = ConfigTypeB::jsonDecode($json);
        static::assertInstanceOf(ConfigTypeA::class, $config);
        static::assertSame($array, $config->toArray());
    }

    /**
     * Data provider of ConfigTypeB configuration for jsonDecode().
     * @return array<int, array<int, array<string, string>|string>>
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
     * @param array<string, string> $array
     * @param string $json
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideJsonDecodeConfigTypeB
     */
    public function testJsonDecodeConfigTypeB(array $array, string $json): void
    {
        $config = ConfigTypeA::jsonDecode($json);
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
            ['{"' . IConfiguration::JSON_CLIENT . '":"001"}'],
            ['{"' . IConfiguration::JSON_USER . '":"username"}'],
            ['{"' . IConfiguration::JSON_PASSWD . '":"password"}']
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
        ConfigTypeA::jsonDecode($json);
    }

    /**
     * Data provider for values, that won't JSON decode to the expected configuration
     * array.
     * @return array<int, array<int, string>>
     */
    public static function provideInvalidJsonString(): array
    {
        return [
            [''],
            ['{'],
            [']'],
            ['71.74'],
            ['806'],
        ];
    }

    /**
     * Test JSON decoding on invalid parameters.
     * @param string $json
     * @dataProvider provideInvalidJsonString
     */
    public function testInvalidJsonString(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON: Expected JSON encoded');
        ConfigTypeA::jsonDecode($json);
    }
}
