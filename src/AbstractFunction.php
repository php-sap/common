<?php

namespace phpsap\classes;

use phpsap\classes\Api\RemoteApi;
use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\interfaces\Api\IApi;
use phpsap\interfaces\IFunction;
use phpsap\interfaces\Util\IJsonSerializable;

/**
 * Class AbstractFunction
 *
 * Manage a SAP remote function call.
 *
 * @package phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
abstract class AbstractFunction extends JsonSerializable implements IFunction
{
    /**
     * @var IConfiguration
     */
    protected $config;

    /**
     * @var string SAP remote function name.
     */
    private $name;

    /**
     * @var RemoteApi[]
     */
    private static $api = [];

    /**
     * Get an array of all valid input parameters.
     * @return array
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    protected function getAllowedKeys()
    {
        $name = $this->getName();
        if (!array_key_exists($name, static::$allowedKeys)) {
            static::$allowedKeys[$name] = [];
            foreach ($this->getApi()->getInputValues() as $input) {
                static::$allowedKeys[$name][] = $input->getName();
            }
            foreach ($this->getApi()->getTables() as $table) {
                static::$allowedKeys[$name][] = $table->getName();
            }
        }
        return static::$allowedKeys[$name];
    }

    /**
     * Initialize the remote function call with at least a name.
     * In order to add SAP remote function call parameters, an API needs to be
     * defined. In case no SAP remote function call API has been defined, it will be
     * queried on the fly by connecting to the SAP remote system. In order to
     * connect to the SAP remote system, you need a connection configuration.
     * @param string                                        $name   SAP remote function name.
     * @param array|null                                    $params SAP remote function call parameters. Default: null
     * @param IConfiguration|null $config Connection configuration. Default: null
     * @param IApi|null              $api    SAP remote function call API. Default: null
     * @throws InvalidArgumentException
     */
    public function __construct($name, array $params = null, IConfiguration $config = null, IApi $api = null)
    {
        parent::__construct();
        $this->setName($name);
        if ($config !== null) {
            $this->config = $config;
        }
        if ($api !== null) {
            $this->setApi($api);
        }
        if ($params !== null) {
            $this->setParams($params);
        }
    }

    /**
     * Set the SAP remote function name.
     * @param string $name
     * @throws InvalidArgumentException
     */
    private function setName($name)
    {
        if (!is_string($name) || empty(trim($name))) {
            throw new InvalidArgumentException(
                'Missing or malformed SAP remote function name'
            );
        }
        $this->name = $name;
    }

    /**
     * Get the SAP remote function name.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the SAP connection configuration for this remote function.
     * @return IConfiguration|null
     */
    public function getConfiguration(): ?IConfiguration
    {
        return $this->config;
    }

    /**
     * Set the SAP connection configuration for this remote function.
     * Using this configuration, the SAP remote function API can be queried and
     * SAP remote function calls can be invoked.
     * @param IConfiguration $config
     * @return $this
     */
    public function setConfiguration(IConfiguration $config): IFunction
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Connect to the SAP remote system and retrieve the API of the SAP remote
     * function. This ignores any API settings in this class.
     * @return RemoteApi
     * @throws IncompleteConfigException
     * @throws ConnectionFailedException
     * @throws UnknownFunctionException
     */
    abstract public function extractApi(): IApi;

    /**
     * Get the remote function API.
     * In case no SAP remote function call API has been defined, it will be queried
     * on the fly by connecting to the SAP remote system.
     * @return RemoteApi
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    public function getApi(): IApi
    {
        $name = $this->getName();
        if (!array_key_exists($name, self::$api)) {
            self::$api[$name] = $this->extractApi();
        }
        return self::$api[$name];
    }

    /**
     * Set the SAP remote function API (e.g. from cache).
     * By setting the API, it will not be queried from the SAP remote system.
     * In order to connect to the SAP remote system, you need a connection
     * configuration- see setConfiguration().
     * @param IApi $api
     * @return $this
     */
    public function setApi(IApi $api): IFunction
    {
        $name = $this->getName();
        self::$api[$name] = $api;
        return $this;
    }

    /**
     * Return a single previously set parameter.
     * @param string $key Name of the parameter to get.
     * @return array|bool|float|int|string
     * @throws InvalidArgumentException
     */
    public function getParam($key)
    {
        return $this->get($key);
    }

    /**
     * Returns all previously set parameters.
     * @return array
     */
    public function getParams(): array
    {
        return $this->toArray();
    }

    /**
     * Set a single SAP remote function call parameter.
     * @param string                      $key   Name of the parameter to set.
     * @param bool|int|float|string|array $value Value of the parameter.
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setParam($key, $value)
    {
        $this->set($key, $value);
        return $this;
    }

    /**
     * Extract all expected SAP remote function call parameters from the given array
     * and set them.
     * @param array $params An array of SAP remote function call parameters.
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setParams(array $params): IFunction
    {
        $this->setMultiple($params);
        return $this;
    }

    /**
     * Remove all SAP remote function call parameters that have been set and start
     * over.
     * @return $this
     */
    public function resetParams(): IFunction
    {
        $this->reset();
        return $this;
    }

    /**
     * Invoke the SAP remote function call with all parameters.
     * Attention: A configuration is necessary to invoke a SAP remote function call!
     * @return array
     * @throws IncompleteConfigException Either a configuration class has not been set,
     *                                                      or it is missing a mandatory configuration key.
     * @throws ConnectionFailedException
     * @throws UnknownFunctionException
     * @throws FunctionCallException
     */
    abstract public function invoke(): array;

    /**
     * @inheritDoc
     * @return array
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    public function jsonSerialize()
    {
        return [
            self::JSON_NAME  => $this->getName(),
            self::JSON_API   => $this->getApi(),
            self::JSON_PARAM => $this->getParams()
        ];
    }

    /**
     * Decode a formerly JSON encoded SAP remote function object.
     * @param string $json
     * @return AbstractFunction
     * @throws InvalidArgumentException
     */
    public static function jsonDecode($json): IJsonSerializable
    {
        $array = static::jsonToArray($json);
        if (
            array_key_exists(self::JSON_NAME, $array)
            && array_key_exists(self::JSON_API, $array)
            && array_key_exists(self::JSON_PARAM, $array)
        ) {
            try {
                $result = new static($array[self::JSON_NAME]);
                $result->setApi(new RemoteApi($array[self::JSON_API]));
                $result->setParams($array[self::JSON_PARAM]);
                return $result;
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid JSON! %s',
                    $exception->getMessage()
                ));
            }
        }
        throw new InvalidArgumentException(sprintf(
            'Invalid JSON! Expected JSON encoded %s string!',
            static::class
        ));
    }
}
