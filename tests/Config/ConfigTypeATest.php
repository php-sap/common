<?php

namespace tests\phpsap\classes\Config;

use phpsap\exceptions\InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use phpsap\classes\Util\JsonSerializable;
use phpsap\interfaces\Config\IConfigTypeA;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\classes\Config\AbstractConfiguration;
use phpsap\classes\Config\ConfigCommon;
use phpsap\classes\Config\ConfigTypeA;
use PHPUnit_Framework_Exception;

/**
 * Class tests\phpsap\classes\Config\ConfigTypeATest
 *
 * Test the type A configuration class.
 *
 * @package tests\phpsap\classes\Config
 * @author  Gregor J.
 * @license MIT
 */
class ConfigTypeATest extends TestCase
{
    /**
     * Test ConfigTypeA inheritance.
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInheritance()
    {
        $config = new ConfigTypeA();
        static::assertInstanceOf(JsonSerializable::class, $config);
        static::assertInstanceOf(IConfiguration::class, $config);
        static::assertInstanceOf(AbstractConfiguration::class, $config);
        static::assertInstanceOf(ConfigCommon::class, $config);
        static::assertInstanceOf(IConfigTypeA::class, $config);
    }

    /**
     * Test set*() and get*() methods.
     */
    public function testSetAndGet()
    {
        $config = new ConfigTypeA();
        $config
            ->setAshost('X2zDYDpwXh')
            ->setSysnr('7789')
            ->setGwhost('rwGslB5foM')
            ->setGwserv('nw1yYwIu2O');
        static::assertSame('X2zDYDpwXh', $config->getAshost());
        static::assertSame('7789', $config->getSysnr());
        static::assertSame('rwGslB5foM', $config->getGwhost());
        static::assertSame('nw1yYwIu2O', $config->getGwserv());
        static::assertJsonStringEqualsJsonString(
            '{"ashost":"X2zDYDpwXh","sysnr":"7789","gwhost":"rwGslB5foM","gwserv":"nw1yYwIu2O"}',
            json_encode($config)
        );
    }
}
