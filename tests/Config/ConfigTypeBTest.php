<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Config;

use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Config\IConfigTypeB;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\classes\Config\AbstractConfiguration;
use phpsap\classes\Config\ConfigCommon;
use phpsap\classes\Config\ConfigTypeB;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

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
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInheritance(): void
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
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws IncompleteConfigException
     * @throws IInvalidArgumentException
     */
    public function testSetAndGet(): void
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
