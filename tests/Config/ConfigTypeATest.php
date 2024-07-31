<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Config;

use JsonException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use phpsap\classes\Util\JsonSerializable;
use phpsap\interfaces\Config\IConfigTypeA;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\classes\Config\ConfigTypeA;

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
    public function testInheritance(): void
    {
        $config = new ConfigTypeA();
        static::assertInstanceOf(JsonSerializable::class, $config);
        static::assertInstanceOf(IConfiguration::class, $config);
        static::assertInstanceOf(IConfigTypeA::class, $config);
    }

    /**
     * Test set*() and get*() methods.
     * @throws ExpectationFailedException
     * @throws IInvalidArgumentException
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSetAndGet(): void
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
            json_encode($config, JSON_THROW_ON_ERROR)
        );
    }
}
