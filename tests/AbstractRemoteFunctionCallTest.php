<?php
/**
 * File src/AbstractRemoteFunctionCallTest.php
 *
 * Test the abstract remote function call.
 *
 * @package common
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\classes;

use kbATeam\TypeCast\TypeCastArray;
use kbATeam\TypeCast\TypeCastValue;
use phpsap\classes\AbstractFunction;
use phpsap\classes\AbstractRemoteFunctionCall;
use phpsap\classes\ConnectionPool;
use phpsap\exceptions\ConnectionNotFoundException;
use phpsap\interfaces\IFunction;
use tests\phpsap\classes\helper\ConfigA;
use tests\phpsap\classes\helper\Connection;
use tests\phpsap\classes\helper\RemoteFunction;
use tests\phpsap\classes\helper\RemoteFunctionCall;

/**
 * Class tests\phpsap\classes\AbstractRemoteFunctionCallTest
 *
 * Test the abstract remote function call.
 *
 * @package tests\phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
class AbstractRemoteFunctionCallTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test class inheritance.
     */
    public function testInheritance()
    {
        $rfc = new RemoteFunctionCall(new ConfigA());
        static::assertInstanceOf(IFunction::class, $rfc);
        static::assertInstanceOf(AbstractRemoteFunctionCall::class, $rfc);
        static::assertInstanceOf(RemoteFunctionCall::class, $rfc);
    }

    /**
     * Test getting a function instance.
     */
    public function testGetFunction()
    {
        $rfc = new RemoteFunctionCall(new ConfigA());
        $rfc->returnName = 'awvovkms';
        $function = $rfc->getFunction();
        static::assertInstanceOf(IFunction::class, $function);
        static::assertInstanceOf(AbstractFunction::class, $function);
        static::assertInstanceOf(RemoteFunction::class, $function);
        static::assertSame('awvovkms', $function->getName());
    }

    /**
     * Test using the same connection config twice which should create only one
     * connection.
     */
    public function testDuplicateConnectionInPool()
    {
        /**
         * Create a connection from a config and add it to the connection pool.
         */
        $config = new ConfigA();
        $connection = new Connection($config);
        ConnectionPool::add($connection->getId(), $connection);
        /**
         * Create a new RFC from the same config, which should add the same
         * connection ID to the pool.
         */
        $rfc = new RemoteFunctionCall($config);
        /**
         * Now remove the connection ID we created and see if the rfc runs into an
         * exception.
         */
        ConnectionPool::remove($connection->getId());
        $this->setExpectedException(
            ConnectionNotFoundException::class,
            sprintf(
                'Connection ID \'%s\' does not exist.',
                $connection->getId()
            )
        );
        $rfc->getFunction();
    }

    /**
     * Test setting a parameter.
     */
    public function testSetParam()
    {
        $rfc = new RemoteFunctionCall(new ConfigA());
        $rfc->setParam('mddaudvn', 613);
        $function = $rfc->getFunction();
        static::assertSame(['mddaudvn' => 613], $function->debugGet('params'));
    }

    /**
     * Test resetting a function call.
     */
    public function testReset()
    {
        $rfc = new RemoteFunctionCall(new ConfigA());
        $rfc->setParam('yovgwyfi', 51.3);
        $rfc->reset();
        $function = $rfc->getFunction();
        static::assertSame([], $function->debugGet('params'));
    }

    /**
     * Test invoking a remote function call without parameters.
     */
    public function testInvoke()
    {
        $rfc = new RemoteFunctionCall(new ConfigA());
        $rfc->getFunction()->results = ['gpgtowzq' => 'C5AWVD1h'];
        $results = $rfc->invoke();
        static::assertSame(['gpgtowzq' => 'C5AWVD1h'], $results);
    }
}
