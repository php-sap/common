<?php

namespace tests\phpsap\classes\Config;

use PHPUnit\Framework\TestCase;
use stdClass;
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
     * @throws \PHPUnit_Framework_Exception
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function testInheritance()
    {
        $config = new ConfigCommonInstance();
        static::assertInstanceOf(JsonSerializable::class, $config);
        static::assertInstanceOf(IConfiguration::class, $config);
        static::assertInstanceOf(AbstractConfiguration::class, $config);
        static::assertInstanceOf(ConfigCommon::class, $config);
    }

    /**
     * Test set*() and get*() methods.
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function testSetAndGet()
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
            ->setCodepage('4442');
        static::assertSame('WaRXigOeCQ', $config->getUser());
        static::assertSame('jn1KWjHeUe', $config->getPasswd());
        static::assertSame('1793', $config->getClient());
        static::assertSame('/H/cFhgo6YkO7/S/7319/H/', $config->getSaprouter());
        static::assertSame(IConfigCommon::TRACE_FULL, $config->getTrace());
        static::assertSame('EN', $config->getLang());
        static::assertSame('B92RGN3jJD', $config->getDest());
        static::assertSame('4442', $config->getCodepage());
    }

    /**
     * Data provider for invalid saprouter values.
     * @return array
     */
    public static function provideInvalidSaprouterValues()
    {
        return [
            [''],
            ['NLo9NXkgZ3'],
            ['dKC3zUbd08:8009'],
            [7725],
            [85.07],
            [true],
            [false],
            [new stdClass()]
        ];
    }

    /**
     * Test invalid saprouter values.
     * @param mixed $value
     * @dataProvider             provideInvalidSaprouterValues
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Expected SAPROUTER to be in following format:
     */
    public function testInvalidSaprouterValues($value)
    {
        (new ConfigCommonInstance())->setSaprouter($value);
    }

    /**
     * Data provider for invalid trace values.
     * @return array
     */
    public static function provideInvalidTraceValues()
    {
        return [
            [''],
            ['5TLzxcsUZr'],
            [4811],
            [-1],
            [1.126],
            [true],
            [false],
            [new stdClass()],
            [[ConfigCommon::TRACE_BRIEF]]
        ];
    }

    /**
     * Test invalid trace values.
     * @param mixed $value
     * @dataProvider provideInvalidTraceValues
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage The trace level can only be 0-3!
     */
    public function testInvalidTraceValues($value)
    {
        (new ConfigCommonInstance())->setTrace($value);
    }

    /**
     * Data provider for invalid lang values.
     * @return array
     */
    public static function provideInvalidLangValues()
    {
        return [
            [''],
            ['e9FB5msjyS'],
            [3349],
            [99.02],
            [true],
            [false],
            [new stdClass()],
            [['EN']]
        ];
    }

    /**
     * Test invalid lang values.
     * @param mixed $value
     * @dataProvider provideInvalidLangValues
     * @expectedException \phpsap\exceptions\InvalidArgumentException
     * @expectedExceptionMessage Expected two letter country code as language!
     */
    public function testInvalidLangValues($value)
    {
        (new ConfigCommonInstance())->setLang($value);
    }
}
