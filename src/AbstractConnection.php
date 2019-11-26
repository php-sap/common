<?php

namespace phpsap\classes;

use stdClass;
use InvalidArgumentException;
use phpsap\interfaces\IConnection;
use phpsap\interfaces\IFunction;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\classes\Config\ConfigCommon;

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
     * @var IConfiguration Connection configuration.
     */
    protected $configuration;

    /**
     * Initialize this class with a configuration.
     * @param string|array|stdClass|IConfiguration $config Connection configuration
     */
    public function __construct($config)
    {
        if (!$config instanceof IConfiguration) {
            $config = ConfigCommon::jsonDecode($config);
        }
        $this->configuration = $config;
    }

    /**
     * Get the configuration of this connection instance.
     * @return IConfiguration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->configuration->jsonSerialize();
    }

    /**
     * Decode a JSON encoded connection configuration.
     * @param string|array|stdClass $config Connection configuration
     * @return IConnection
     */
    public static function jsonDecode($config)
    {
        return new static($config);
    }

    /**
     * Prepare a remote function call and return a function instance.
     * @param string $functionName
     * @return IFunction
     * @throws ConnectionFailedException
     * @throws UnknownFunctionException
     * @throws IncompleteConfigException
     */
    public function prepareFunction($functionName)
    {
        if (!is_string($functionName) || empty(trim($functionName))) {
            throw new InvalidArgumentException(
                'Missing or malformed SAP remote function name'
            );
        }
        return $this->createFunctionInstance($functionName);
    }

    /**
     * Prepare a remote function call and return a function instance.
     * @param string $name
     * @return \phpsap\classes\AbstractFunction
     * @throws ConnectionFailedException
     * @throws UnknownFunctionException
     * @throws IncompleteConfigException
     */
    abstract protected function createFunctionInstance($name);
}
