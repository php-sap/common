<?php

namespace tests\phpsap\classes;

use phpsap\interfaces\IConnection;
use phpsap\classes\AbstractConnection;
use phpsap\classes\Config\ConfigTypeA;
use tests\phpsap\classes\helper\Connection;
use tests\phpsap\classes\helper\RemoteFunction;

/**
 * Class tests\phpsap\classes\AbstractConnectionTest
 *
 * Test the functionality of the AbstractConnection class.
 *
 * @package tests\phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
class AbstractConnectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test connection instance inheritance.
     */
    public function testInheritance()
    {
        $connection = new Connection(new ConfigTypeA());
        static::assertInstanceOf(IConnection::class, $connection);
        static::assertInstanceOf(AbstractConnection::class, $connection);
        static::assertInstanceOf(Connection::class, $connection);
    }

    /**
     * Test basic JSON serialization.
     */
    public function testBasicJsonSerialization()
    {
        $config = new ConfigTypeA();
        static::assertJsonStringEqualsJsonString('{}', json_encode($config));
        $connection = new Connection($config);
        static::assertJsonStringEqualsJsonString('{}', json_encode($connection));
    }

    /**
     * Data provider for valid configuration variants with ASHOST = a1seera3 and
     * SYSNR = 3374 (config type A).
     * @return array
     */
    public static function provideConnectionConfigurationVariant()
    {
        $config = (new ConfigTypeA())
            ->setAshost('a1seera3')
            ->setSysnr('3374')
        ;
        return [
            [$config],
            [json_encode($config)],
            [json_decode(json_encode($config))],
            [json_decode(json_encode($config), true)]
        ];
    }

    /**
     * Test connection configuration variants.
     * @param string|array|\phpsap\interfaces\Config\IConfiguration $config
     * @dataProvider provideConnectionConfigurationVariant
     */
    public function testConnectionConfigurationVariant($config)
    {
        $connection = new Connection($config);
        /**
         * @var ConfigTypeA $configuration
         */
        $configuration = $connection->getConfiguration();
        static::assertInstanceOf(ConfigTypeA::class, $configuration);
        $actual = json_encode($configuration);
        $expected = '{"ashost":"a1seera3","sysnr":"3374"}';
        static::assertJsonStringEqualsJsonString($expected, $actual);
    }

    public function testJsonDecode()
    {
        $json = '{"ashost":"5fu2gbae","sysnr":"7443"}';
        $connection = Connection::jsonDecode($json);
        static::assertInstanceOf(IConnection::class, $connection);
        static::assertInstanceOf(AbstractConnection::class, $connection);
        /**
         * @var ConfigTypeA $configuration
         */
        $configuration = $connection->getConfiguration();
        static::assertInstanceOf(ConfigTypeA::class, $configuration);
        static::assertSame('5fu2gbae', $configuration->getAshost());
        static::assertSame('7443', $configuration->getSysnr());
    }

    /**
     * Test preparing a function.
     */
    public function testPrepareFunction()
    {
        $connection = new Connection(new ConfigTypeA());
        static::assertInstanceOf(Connection::class, $connection);
        $function = $connection->prepareFunction('mhcbyejv');
        static::assertInstanceOf(RemoteFunction::class, $function);
        static::assertSame('mhcbyejv', $function->getName());
    }

    /**
     * Data provider of invalid function names.
     * @return array
     */
    public static function invalidFunctionNames()
    {
        return [
            [' '],
            [85],
            [1.4],
            [['xlpbzllb']],
            [true],
            [false],
            [null],
            ["\t"],
            [new \stdClass()]
        ];
    }

    /**
     * Test exception thrown upon invalid function names.
     * @param mixed $name
     * @dataProvider invalidFunctionNames
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Missing or malformed SAP remote function name
     */
    public function testInvalidFunctionNames($name)
    {
        $connection = new Connection(new ConfigTypeA());
        static::assertInstanceOf(Connection::class, $connection);
        $connection->prepareFunction($name);
    }
}
