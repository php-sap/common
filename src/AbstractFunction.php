<?php

declare(strict_types=1);

namespace phpsap\classes;

use JsonException;
use phpsap\classes\Api\RemoteApi;
use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\exceptions\FunctionCallException;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\exceptions\UnknownFunctionException;
use phpsap\interfaces\Config\IConfiguration;
use phpsap\interfaces\Api\IApi;
use phpsap\interfaces\exceptions\IConnectionFailedException;
use phpsap\interfaces\exceptions\IIncompleteConfigException;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use phpsap\interfaces\exceptions\IUnknownFunctionException;
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
     * @var null|IConfiguration
     */
    private ?IConfiguration $config = null;

    /**
     * @var string SAP remote function name.
     */
    private string $name = '';

    /**
     * @var IApi[]
     */
    private static array $api = [];

    /**
     * @param array<int|string, mixed> $array
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws InvalidArgumentException
     * @throws UnknownFunctionException
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(array $array)
    {
        $this->reset();
        if(!array_key_exists(self::JSON_NAME, $array) || !is_string($array[self::JSON_NAME])) {
            throw new InvalidArgumentException(
                sprintf('Missing %s "%s"', static::class, self::JSON_NAME)
            );
        }
        $this->setName($array[self::JSON_NAME]);
        if (array_key_exists(self::JSON_API, $array)) {
            $this->setApi(new RemoteApi($array[self::JSON_API]));
        }
        if (array_key_exists(self::JSON_PARAM, $array)) {
            $this->setParams($array[self::JSON_PARAM]);
        }
    }

    /**
     * Get an array of all valid input parameters.
     * @return array<int, string>
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     */
    protected function getAllowedKeys(): array
    {
        $name = $this->getName();
        static $allowed_keys = [];
        if (!array_key_exists($name, $allowed_keys)) {
            $allowed_keys[$name] = [];
            foreach ($this->getApi()->getInputElements() as $input) {
                $allowed_keys[$name][] = $input->getName();
            }
            foreach ($this->getApi()->getTables() as $table) {
                $allowed_keys[$name][] = $table->getName();
            }
        }
        return $allowed_keys[$name];
    }

    /**
     * Initialize the remote function call with at least a name.
     * In order to add SAP remote function call parameters, an API needs to be
     * defined. In case no SAP remote function call API has been defined, it will be
     * queried on the fly by connecting to the SAP remote system. In order to
     * connect to the SAP remote system, you need a connection configuration.
     * @param string $name SAP remote function name.
     * @param null|array<string, null|bool|int|float|string|array<int|string, mixed>> $params SAP remote function call parameters. Default: null
     * @param IConfiguration|null $config Connection configuration. Default: null
     * @param IApi|null $api SAP remote function call API. Default: null
     * @throws InvalidArgumentException
     * @throws IConnectionFailedException
     * @throws IIncompleteConfigException
     * @throws IInvalidArgumentException
     * @throws IUnknownFunctionException
     */
    public static function create(string $name, ?array $params = null, ?IConfiguration $config = null, ?IApi $api = null): IFunction
    {
        $function = new static([self::JSON_NAME => trim($name)]);
        if ($config !== null) {
            $function->setConfiguration($config);
        }
        if ($api !== null) {
            $function->setApi($api);
        }
        if ($params !== null) {
            $function->setParams($params);
        }
        return $function;
    }

    /**
     * Set the SAP remote function name.
     * @param string $name
     * @throws InvalidArgumentException
     */
    private function setName(string $name): void
    {
        $name = trim($name);
        if ($name === '') {
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
     * @throws IncompleteConfigException
     */
    public function getConfiguration(): ?IConfiguration
    {
        if ($this->config !== null) {
            return $this->config;
        }
        throw new IncompleteConfigException(
            sprintf('Missing configuration for "%s"!', $this->getName())
        );
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
     * @return IApi
     * @throws IncompleteConfigException
     * @throws ConnectionFailedException
     * @throws UnknownFunctionException
     */
    abstract public function extractApi(): IApi;

    /**
     * Get the remote function API.
     * In case no SAP remote function call API has been defined, it will be queried
     * on the fly by connecting to the SAP remote system.
     * @return IApi
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
     * @return array<int|string, mixed>|bool|float|int|string|null
     * @throws InvalidArgumentException
     */
    public function getParam(string $key): float|array|bool|int|string|null
    {
        return $this->get($key);
    }

    /**
     * Returns all previously set parameters.
     * @return array<string, mixed>
     */
    public function getParams(): array
    {
        return $this->toArray();
    }

    /**
     * Set a single SAP remote function call parameter.
     * @param string $key   Name of the parameter to set.
     * @param array<int|string, mixed>|bool|float|int|string $value Value of the parameter.
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setParam(string $key, float|int|bool|array|string $value): AbstractFunction
    {
        $this->set($key, $value);
        return $this;
    }

    /**
     * Extract all expected SAP remote function call parameters from the given array
     * and set them.
     * @param array<string, null|bool|int|float|string|array<int|string, mixed>> $params An array of SAP remote function call parameters.
     * @return $this
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws InvalidArgumentException
     * @throws UnknownFunctionException
     */
    public function setParams(array $params): IFunction
    {
        foreach ($this->getAllowedKeys() as $key) {
            if (!array_key_exists($key, $params)) {
                throw new InvalidArgumentException(sprintf(
                    '%s is missing parameter key %s!',
                    static::class,
                    $key
                ));
            }
            $this->set($key, $params[$key]);
        }
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
     * @return array<string, mixed>
     * @throws IncompleteConfigException Either a configuration class has not been set,
     *                                                      or it is missing a mandatory configuration key.
     * @throws ConnectionFailedException
     * @throws UnknownFunctionException
     * @throws FunctionCallException
     */
    abstract public function invoke(): array;

    /**
     * @inheritDoc
     * @return array<string, string|IApi|array<string, mixed>>
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws UnknownFunctionException
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function jsonSerialize(): array
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
     * @return IFunction
     * @throws ConnectionFailedException
     * @throws IncompleteConfigException
     * @throws InvalidArgumentException
     * @throws UnknownFunctionException
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function jsonDecode(string $json): IFunction
    {
        try {
            $array = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            /**
             * JSON might decode into anything but array without an error.
             */
            if (!is_array($array)) {
                throw new InvalidArgumentException('JSON did not decode into an array!');
            }
            /**
             * Ensure all mandatory keys are set.
             */
            if (
                !array_key_exists(self::JSON_NAME, $array)
                || !array_key_exists(self::JSON_API, $array)
                || !array_key_exists(self::JSON_PARAM, $array)
            ) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Missing mandatory keys %s, %s, %s!',
                        self::JSON_NAME,
                        self::JSON_API,
                        self::JSON_PARAM
                    ),
                );
            }
            /**
             * Use the constructor of the implementing class.
             */
            return new static($array);
        } catch (InvalidArgumentException|JsonException $exception) {
            throw new InvalidArgumentException(
                sprintf('Invalid JSON: Expected JSON encoded %s!', static::class),
                0,
                $exception
            );
        }
    }
}
