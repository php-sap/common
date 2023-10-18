<?php

namespace tests\phpsap\classes\Config;

use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Config\IConfigTypeB;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\classes\Config\AbstractConfiguration;
use phpsap\classes\Config\ConfigCommon;
use phpsap\classes\Config\ConfigTypeB;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Exception;

/**
 * Class tests\phpsap\classes\Config\ConfigTypeBTest
 *
 * Test the type B configuration class.
 *
 * @package tests\phpsap\classes\Config
 * @author  Gregor J.
 * @license MIT
 */
class ConfigTypeBTest extends TestCase
{
    /**
     * Test ConfigTypeB inheritance.
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidArgumentException
     */
    public function testInheritance()
    {
        $config = new ConfigTypeB();
        static::assertInstanceOf(JsonSerializable::class, $config);
        static::assertInstanceOf(IConfiguration::class, $config);
        static::assertInstanceOf(AbstractConfiguration::class, $config);
        static::assertInstanceOf(ConfigCommon::class, $config);
        static::assertInstanceOf(IConfigTypeB::class, $config);
    }

    /**
     * Test set*() and get*() methods.
     */
    public function testSetAndGet()
    {
        $config = new ConfigTypeB();
        $config
            ->setMshost('caum5mXQaN')
            ->setR3name('D3Y3HWdOMX')
            ->setGroup('AyRc4bxpQj');
        static::assertSame('caum5mXQaN', $config->getMshost());
        static::assertSame('D3Y3HWdOMX', $config->getR3name());
        static::assertSame('AyRc4bxpQj', $config->getGroup());
        static::assertJsonStringEqualsJsonString(
            '{"mshost":"caum5mXQaN","r3name":"D3Y3HWdOMX","group":"AyRc4bxpQj"}',
            json_encode($config)
        );
    }
}
