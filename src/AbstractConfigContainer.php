<?php
/**
 * File src/AbstractConfigContainer.php
 *
 * PSR-11 implementation of a simple container storing configuration keys and values.
 *
 * @package common
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\classes;

use InvalidArgumentException;
use JsonSerializable;
use phpsap\exceptions\ConfigKeyNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class phpsap\classes\AbstractConfigContainer
 *
 * PSR-11 implementation of a simple container storing configuration keys and values.
 *
 * @package phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
abstract class AbstractConfigContainer implements ContainerInterface, JsonSerializable
{
    /**
     * @var array configuration data
     */
    private $config;

    /**
     * Constructor optionally loads the configuration either from a JSON encoded
     * string or from an array.
     * @param array|string $config the configuration
     * @throws \InvalidArgumentException
     */
    public function __construct($config = null)
    {
        $this->config = [];
        if (is_array($config)) {
            $this->loadArray($config);
        } elseif (is_string($config)) {
            $this->loadJsonString($config);
        } elseif ($config !== null) {
            throw new InvalidArgumentException(
                'Expected configuration to either be an array'
                . ', or a JSON encoded string!'
            );
        }
    }

    /**
     * Load configuration array.
     * @param array $config
     * @throws \InvalidArgumentException
     */
    protected function loadArray($config)
    {
        if (!is_array($config)) {
            throw new InvalidArgumentException(
                'Expected config to be an array!'
            );
        }
        foreach ($config as $key => $value) {
            $key = strtolower($key);
            $method = sprintf('set%s', ucfirst($key));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * Load configuration from a JSON string.
     * @param string $config
     * @throws \InvalidArgumentException
     */
    protected function loadJsonString($config)
    {
        if (!is_string($config)) {
            throw new InvalidArgumentException(
                'Expected config to be a JSON encoded string!'
            );
        }
        $configArr = json_decode($config, true);
        if ($configArr === null) {
            throw new InvalidArgumentException(
                'Expected config to be a JSON encoded string!'
            );
        }
        $this->loadArray($configArr);
    }

    /**
     * Sets a configuration value.
     * @param string $key
     * @param string|int|float|bool $value
     */
    protected function set($key, $value)
    {
        if (!is_string($key) || empty($key)) {
            throw new InvalidArgumentException(
                'Expected configuration key to be a string value.'
            );
        }
        if (!is_string($value) && !is_int($value) && !is_float($value) && !is_bool($value)) {
            throw new InvalidArgumentException(
                'Expected configuration value to be either a'
                .' string, floating point, integer or boolean value.'
            );
        }
        $this->config[$key] = $value;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $key Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this**
     *                                     identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($key)
    {
        if (!is_string($key) || empty($key)) {
            throw new InvalidArgumentException(
                'Expected configuration key to be a string value.'
            );
        }
        if (!$this->has($key)) {
            throw new ConfigKeyNotFoundException(sprintf(
                'No entry was found for configuration key \'%s\'.',
                $key
            ));
        }
        return $this->config[$key];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an
     * exception. It does however mean that `get($id)` will not throw a
     * `NotFoundExceptionInterface`.
     *
     * @param string $key Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($key)
    {
        if (!is_string($key) || empty($key)) {
            throw new InvalidArgumentException(
                'Expected configuration key to be a string value.'
            );
        }
        return array_key_exists($key, $this->config);
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
        return $this->config;
    }

    /**
     * Generate the type of configuration needed by the PHP module in order to
     * establish a connection to SAP.
     * @return mixed
     * @throws \phpsap\exceptions\IncompleteConfigException
     */
    abstract public function generateConfig();
}
