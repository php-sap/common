<?php

namespace phpsap\classes;

use phpsap\classes\Api\RemoteApi;
use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\interfaces\Api\IApi;
use phpsap\interfaces\IFunction;

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
     * @var \phpsap\interfaces\Config\IConfiguration
     */
    protected $config;

    /**
     * @var string SAP remote function name.
     */
    private $name;

    /**
     * @var \phpsap\classes\Api\RemoteApi[]
     */
    private static $api = [];

    /**
     * Get an array of all valid input parameters.
     * @return array
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\IncompleteConfigException
     * @throws \phpsap\exceptions\UnknownFunctionException
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
     * @param \phpsap\interfaces\Config\IConfiguration|null $config Connection configuration. Default: null
     * @param \phpsap\interfaces\Api\IApi|null              $api    SAP remote function call API. Default: null
     * @throws \phpsap\exceptions\InvalidArgumentException
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
     * @throws \phpsap\exceptions\InvalidArgumentException
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
     * @return \phpsap\interfaces\Config\IConfiguration|null
     */
    public function getConfiguration(): ?IConfiguration
    {
        return $this->config;
    }

    /**
     * Set the SAP connection configuration for this remote function.
     * Using this configuration, the SAP remote function API can be queried and
     * SAP remote function calls can be invoked.
     * @param \phpsap\interfaces\Config\IConfiguration $config
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
     * @return \phpsap\classes\Api\RemoteApi
     * @throws \phpsap\exceptions\IncompleteConfigException
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    abstract public function extractApi(): IApi;

    /**
     * Get the remote function API.
     * In case no SAP remote function call API has been defined, it will be queried
     * on the fly by connecting to the SAP remote system.
     * @return \phpsap\classes\Api\RemoteApi
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\IncompleteConfigException
     * @throws \phpsap\exceptions\UnknownFunctionException
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
     * @param \phpsap\interfaces\Api\IApi $api
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
     * @throws \phpsap\exceptions\InvalidArgumentException
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
     * @throws \phpsap\exceptions\InvalidArgumentException
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
     * @throws \phpsap\exceptions\InvalidArgumentException
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
     * @throws \phpsap\exceptions\IncompleteConfigException Either a configuration class has not been set,
     *                                                      or it is missing a mandatory configuration key.
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\UnknownFunctionException
     * @throws \phpsap\exceptions\FunctionCallException
     */
    abstract public function invoke(): array;

    /**
     * @inheritDoc
     * @return array
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\IncompleteConfigException
     * @throws \phpsap\exceptions\UnknownFunctionException
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
     * @return \phpsap\classes\AbstractFunction
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public static function jsonDecode($json): \phpsap\interfaces\Util\IJsonSerializable
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
