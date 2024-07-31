<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Config\Traits;

use phpsap\classes\Config\ConfigTypeA;
use phpsap\classes\Config\ConfigTypeB;
use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

/**
 * Class CommonTraitTest
 */
class CommonTraitTest extends TestCase
{
    /**
     * @return array<int, array<int, ConfigTypeA|ConfigTypeB>>
     */
    public static function provideTypeAB(): array
    {
        return [
            [new ConfigTypeA()],
            [new ConfigTypeB()],
        ];
    }

    /**
     * Test inheritance
     * @param ConfigTypeA|ConfigTypeB $config
     * @return void
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideTypeAB
     */
    public function testInheritance(ConfigTypeA|ConfigTypeB $config): void
    {
        static::assertInstanceOf(JsonSerializable::class, $config);
        static::assertInstanceOf(IConfiguration::class, $config);
    }

    /**
     * Test set*() and get*() methods.
     * @param ConfigTypeA|ConfigTypeB $config
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws IncompleteConfigException
     * @throws IInvalidArgumentException
     * @dataProvider provideTypeAB
     */
    public function testSetAndGet(ConfigTypeA|ConfigTypeB $config): void
    {
        $config
            ->setUser('WaRXigOeCQ')
            ->setPasswd('jn1KWjHeUe')
            ->setClient('1793')
            ->setSaprouter('/H/cFhgo6YkO7/S/7319/H/')
            ->setTrace(IConfiguration::TRACE_FULL)
            ->setLang('EN')
            ->setDest('B92RGN3jJD')
            ->setCodepage(4442);
        static::assertSame('WaRXigOeCQ', $config->getUser());
        static::assertSame('jn1KWjHeUe', $config->getPasswd());
        static::assertSame('1793', $config->getClient());
        static::assertSame('/H/cFhgo6YkO7/S/7319/H/', $config->getSaprouter());
        static::assertSame(IConfiguration::TRACE_FULL, $config->getTrace());
        static::assertSame('EN', $config->getLang());
        static::assertSame('B92RGN3jJD', $config->getDest());
        static::assertSame(4442, $config->getCodepage());
    }

    /**
     * Data provider for invalid saprouter values.
     * @return array<int, array<int, string>>
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
     * @param string $value
     * @dataProvider             provideInvalidSaprouterValues
     */
    public function testInvalidSaprouterValues(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected SAPROUTER to be in following format:');
        (new ConfigTypeA())->setSaprouter($value);
    }


    /**
     * Data provider for invalid trace values.
     * @return array<int, array<int, int>>
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
        (new ConfigTypeB())->setTrace($value);
    }

    /**
     * Data provider for invalid lang values.
     * @return array<int, array<int, string>>
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
     * @param string $value
     * @dataProvider provideInvalidLangValues
     */
    public function testInvalidLangValues(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected two letter country code as language!');
        (new ConfigTypeA())->setLang($value);
    }
}
