<?php

namespace tests\phpsap\classes\helper;

use phpsap\classes\AbstractConnection;
use phpsap\exceptions\ConnectionFailedException;

/**
 * Class tests\phpsap\classes\helper\Connection
 *
 * Helper class extending the abstract connection class for testing.
 *
 * @package tests\phpsap\classes\helper
 * @author  Gregor J.
 * @license MIT
 */
class Connection extends AbstractConnection
{
    /**
     * @var int
     */
    public $pingResult;

    /**
     * @var mixed
     */
    public $connectionRessource = 'hello world!';

    /**
     * Send a ping request via an established connection to verify that the
     * connection works.
     * @return boolean success?
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    public function ping()
    {
        if (!is_bool($this->pingResult)) {
            $this->connection = null;
            throw new ConnectionFailedException('Connection failed.');
        }
        return $this->pingResult;
    }

    /**
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    public function connect()
    {
        $this->connection = $this->connectionRessource;
    }

    /**
     * Closes the connection instance of the underlying PHP module.
     */
    public function close()
    {
        $this->connection = null;
    }

    /**
     * Prepare a remote function call and return a function instance.
     * @param string $name
     * @return \tests\phpsap\classes\helper\RemoteFunction
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    protected function createFunctionInstance($name)
    {
        return new RemoteFunction($this->connection, $name);
    }

    /**
     * Make protected function public for testing.
     * Returns the actual connection ressource.
     * @return mixed SAPRFC connection instance
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    public function getConnection()
    {
        return parent::getConnection();
    }
}
