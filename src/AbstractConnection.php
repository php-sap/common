<?php

namespace phpsap\classes;

use InvalidArgumentException;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\interfaces\IConfig;
use phpsap\interfaces\IConnection;

/**
 * Class phpsap\classes\AbstractConnection
 *
 * Abstract class to manage a single PHP/SAP connection.
 *
 * @package phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
abstract class AbstractConnection implements IConnection
{
    /**
     * @var mixed SAP remote connection.
     */
    protected $connection;

    /**
     * @var array Configuration array.
     */
    protected $config;

    /**
     * @var string SapRfcConnection ID.
     */
    protected $connectionId;

    /**
     * Initialize this class with a configuration.
     * @param \phpsap\interfaces\IConfig $config
     * @throws \phpsap\interfaces\exceptions\IIncompleteConfigException
     */
    public function __construct(IConfig $config)
    {
        $this->config = $config->generateConfig();
    }

    /**
     * Returns the connection ID.
     * The connection ID is derived from the configuration. The same configuration
     * will result in the same connection ID.
     * @return string
     */
    public function getId()
    {
        if ($this->connectionId === null) {
            $this->connectionId = md5(serialize($this->config));
        }
        return $this->connectionId;
    }

    /**
     * Is connection established?
     * @return bool
     */
    public function isConnected()
    {
        return $this->connection !== null;
    }

    /**
     * Returns the actual connection resource.
     * @return mixed SAPRFC connection instance
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    protected function getConnection()
    {
        if (!$this->isConnected()) {
            $this->connect();
        }
        if ($this->connection === null) {
            throw new ConnectionFailedException(sprintf(
                'Connection %s failed.',
                $this->getId()
            ));
        }
        return $this->connection;
    }

    /**
     * Prepare a remote function call and return a function instance.
     * @param string $name
     * @return \phpsap\classes\AbstractFunction
     * @throws \InvalidArgumentException
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    public function prepareFunction($name)
    {
        if (!is_string($name) || empty(trim($name))) {
            throw new InvalidArgumentException(
                'Missing or malformed SAP remote function name'
            );
        }
        return $this->createFunctionInstance(trim($name));
    }

    /**
     * Establish a connection to the configured system.
     * In case the connection is already open, close it first.
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    abstract public function connect();

    /**
     * Send a ping request via an established connection to verify that the
     * connection works.
     * @return boolean success?
     * @throws \phpsap\exceptions\ConnectionFailedException
     */
    abstract public function ping();

    /**
     * Closes the connection instance of the underlying PHP module.
     */
    abstract public function close();

    /**
     * Prepare a remote function call and return a function instance.
     * @param string $name
     * @return \phpsap\classes\AbstractFunction
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    abstract protected function createFunctionInstance($name);
}
