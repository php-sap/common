<?php

namespace tests\phpsap\classes;

use phpsap\classes\AbstractConnection;
use phpsap\interfaces\IConnection;
use tests\phpsap\classes\helper\Connection;
use tests\phpsap\classes\helper\ConfigA;
use tests\phpsap\classes\helper\RemoteFunction;

/**
 * Class tests\phpsap\classes\AbstractConnectionTest
 *
 * DESCRIPTION
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
        $connection = new Connection(new ConfigA());
        static::assertInstanceOf(IConnection::class, $connection);
        static::assertInstanceOf(AbstractConnection::class, $connection);
        static::assertInstanceOf(Connection::class, $connection);
    }

    /**
     * Test getting the connection ID.
     */
    public function testGetId()
    {
        $config = new ConfigA();
        $connection = new Connection($config);
        static::assertInstanceOf(Connection::class, $connection);
        $actual = $connection->getId();
        $expected = md5(serialize($config->generateConfig()));
        static::assertSame($expected, $actual);
    }

    /**
     * Test successfully pinging the connection.
     */
    public function testSuccessfulPing()
    {
        $connection = new Connection(new ConfigA());
        static::assertInstanceOf(Connection::class, $connection);
        $connection->pingResult = false;
        static::assertFalse($connection->ping());
        $connection->pingResult = true;
        static::assertTrue($connection->ping());
    }

    /**
     * @expectedException \phpsap\exceptions\ConnectionFailedException
     * @expectedExceptionMessage Connection failed.
     */
    public function testPingConnectionFailedException()
    {
        $connection = new Connection(new ConfigA());
        static::assertInstanceOf(Connection::class, $connection);
        $connection->pingResult = null;
        $connection->ping();
    }

    /**
     * Test internal function for establishing a connection.
     */
    public function testSuccessfulGetConnection()
    {
        $connection = new Connection(new ConfigA());
        static::assertInstanceOf(Connection::class, $connection);
        $connection->connectionRessource = 'success';
        $backend_connection = $connection->getConnection();
        static::assertSame('success', $backend_connection);
    }

    /**
     * Test internal function for establishing a connection.
     * @expectedException \phpsap\exceptions\ConnectionFailedException
     * @expectedExceptionMessageRegExp "^Connection [0-9a-f]{32} failed.$"
     */
    public function testFailedGetConnection()
    {
        $connection = new Connection(new ConfigA());
        static::assertInstanceOf(Connection::class, $connection);
        $connection->connectionRessource = null;
        $connection->getConnection();
    }

    /**
     * Test internal function isConnected().
     */
    public function testIsConnected()
    {
        $connection = new Connection(new ConfigA());
        static::assertInstanceOf(Connection::class, $connection);
        static::assertFalse($connection->isConnected());
        $connection->connectionRessource = 'success';
        $ressource = $connection->getConnection();
        static::assertTrue($connection->isConnected());
        static::assertSame('success', $ressource);
    }

    /**
     * Test preparing a function.
     */
    public function testPrepareFunction()
    {
        $connection = new Connection(new ConfigA());
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
        $connection = new Connection(new ConfigA());
        static::assertInstanceOf(Connection::class, $connection);
        $connection->prepareFunction($name);
    }
}
