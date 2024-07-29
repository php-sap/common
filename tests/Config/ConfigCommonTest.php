<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Config;

use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use phpsap\classes\Util\JsonSerializable;
use phpsap\interfaces\Config\IConfigCommon;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\classes\Config\AbstractConfiguration;
use phpsap\classes\Config\ConfigCommon;
use tests\phpsap\classes\helper\ConfigCommonInstance;

/**
 * Class tests\phpsap\classes\Config\ConfigCommonTest
 *
 * Test the common configuration class via the proxy class
 * CommonConfigurationInstance.
 *
 * @package tests\phpsap\classes\Config
 * @author  Gregor J.
 * @license MIT
 */
class ConfigCommonTest extends TestCase
{
    /**
     * Test ConfigCommon inheritance.
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInheritance(): void
    {
        $config = new ConfigCommonInstance();
        static::assertInstanceOf(JsonSerializable::class, $config);
        static::assertInstanceOf(IConfiguration::class, $config);
        static::assertInstanceOf(AbstractConfiguration::class, $config);
        static::assertInstanceOf(ConfigCommon::class, $config);
    }

    /**
     * Test set*() and get*() methods.
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws IncompleteConfigException
     * @throws IInvalidArgumentException
     */
    public function testSetAndGet(): void
    {
        $config = new ConfigCommonInstance();
        $config
            ->setUser('WaRXigOeCQ')
            ->setPasswd('jn1KWjHeUe')
            ->setClient('1793')
            ->setSaprouter('/H/cFhgo6YkO7/S/7319/H/')
            ->setTrace(IConfigCommon::TRACE_FULL)
            ->setLang('EN')
            ->setDest('B92RGN3jJD')
            ->setCodepage(4442);
        static::assertSame('WaRXigOeCQ', $config->getUser());
        static::assertSame('jn1KWjHeUe', $config->getPasswd());
        static::assertSame('1793', $config->getClient());
        static::assertSame('/H/cFhgo6YkO7/S/7319/H/', $config->getSaprouter());
        static::assertSame(IConfigCommon::TRACE_FULL, $config->getTrace());
        static::assertSame('EN', $config->getLang());
        static::assertSame('B92RGN3jJD', $config->getDest());
        static::assertSame(4442, $config->getCodepage());
    }

    /**
     * Data provider for invalid saprouter values.
     * @return array<int, array<int, mixed>>
     */
    public static function provideInvalidSaprouterValues(): array
    {
        return [
            [''],
            ['NLo9NXkgZ3'],
            ['dKC3zUbd08:8009']
        ];
    }

    /**
     * Test invalid saprouter values.
     * @param mixed $value
     * @dataProvider             provideInvalidSaprouterValues
     */
    public function testInvalidSaprouterValues(mixed $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected SAPROUTER to be in following format:');
        (new ConfigCommonInstance())->setSaprouter($value);
    }

    /**
     * Data provider for invalid trace values.
     * @return array
     */
    public static function provideInvalidTraceValues(): array
    {
        return [
            [4811],
            [-1],
        ];
    }

    /**
     * Test invalid trace values.
     * @param int $value
     * @dataProvider provideInvalidTraceValues
     */
    public function testInvalidTraceValues(int $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The trace level can only be 0-3!');
        (new ConfigCommonInstance())->setTrace($value);
    }

    /**
     * Data provider for invalid lang values.
     * @return array<int, array<int, mixed>>
     */
    public static function provideInvalidLangValues(): array
    {
        return [
            [''],
            ['e9FB5msjyS']
        ];
    }

    /**
     * Test invalid lang values.
     * @param mixed $value
     * @dataProvider provideInvalidLangValues
     */
    public function testInvalidLangValues(mixed $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected two letter country code as language!');
        (new ConfigCommonInstance())->setLang($value);
    }
}
