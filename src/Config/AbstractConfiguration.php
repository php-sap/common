<?php

namespace phpsap\classes\Config;

use InvalidArgumentException;
use LogicException;
use stdClass;
use phpsap\classes\Config\ConfigTypeA;
use phpsap\classes\Config\ConfigTypeB;
use phpsap\exceptions\ConfigKeyNotFoundException;
use phpsap\interfaces\Config\IConfiguration;

/**
 * Class phpsap\classes\Config\AbstractConfiguration
 *
 * This class reads, writes and removes configuration keys and their values.
 *
 * @package phpsap\classes\Config
 * @author  Gregor J.
 * @license MIT
 */
abstract class AbstractConfiguration implements IConfiguration
{
    /**
     * @var stdClass The configuration data.
     */
    private $config;

    /**
     * Load the configuration either from a JSON encoded string or from an array.
     * @param array|string|stdClass $config the configuration
     * @throws InvalidArgumentException In case the configuration is neither JSON
     *                                  nor an array.
     */
    public function __construct($config = null)
    {
        $this->config = new stdClass();
        if ($config === null) {
            return;
        }
        $config = static::convertConfigToArray($config);
        foreach (static::getValidConfigKeys() as $key) {
            if (array_key_exists($key, $config)) {
                $method = sprintf('set%s', ucfirst($key));
                $this->{$method}($config[$key]);
            }
        }
    }

    /**
     * Retrieves a configuration value for a given key.
     * @param string $key
     * @return string|int
     * @throws LogicException In case of an invalid configuration key.
     * @throws ConfigKeyNotFoundException In case the key doesn't exist for the
     *                                    current SAP configuration type.
     */
    protected function get($key)
    {
        if (!$this->has($key)) {
            throw new ConfigKeyNotFoundException(sprintf(
                'Configuration key \'%s\' not found!',
                $key
            ));
        }
        return $this->config->{$key};
    }

    /**
     * Sets a configuration value for a given key.
     * @param string     $key
     * @param string|int $value
     * @return IConfiguration
     * @throws LogicException In case of an invalid configuration key.
     * @throws InvalidArgumentException In case of an invalid configuration value.
     */
    protected function set($key, $value)
    {
        if (!is_string($key) || empty($key)) {
            throw new LogicException(
                'Expected configuration key to be a string value.'
            );
        }
        if (!in_array($key, static::getValidConfigKeys(), true)) {
            throw new LogicException(sprintf('Unknown configuration key \'%s\'!', $key));
        }
        if ($value === null) {
            $this->remove($key);
            return $this;
        }
        if (is_string($value) || is_int($value)) {
            $this->config->{$key} = $value;
            return $this;
        }
        throw new InvalidArgumentException(sprintf(
            'Expected configuration value for \'%s\' to either be a string'
            . ' or integer value!',
            $key
        ));
    }

    /**
     * Removes a configuration value for a given key.
     * @param string $key
     * @return IConfiguration
     * @throws LogicException In case of an invalid configuration key.
     */
    protected function remove($key)
    {
        if ($this->has($key)) {
            unset($this->config->{$key});
        }
        return $this;
    }

    /**
     * Returns true if the configuration has a value for the given key.
     * @param string $key
     * @return bool
     * @throws LogicException In case of an invalid configuration key.
     */
    protected function has($key)
    {
        if (!is_string($key) || empty($key)) {
            throw new LogicException(
                'Expected configuration key to be a string value.'
            );
        }
        if (!in_array($key, static::getValidConfigKeys(), true)) {
            throw new LogicException(sprintf('Unknown configuration key \'%s\'!', $key));
        }
        return property_exists($this->config, $key);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return stdClass data which can be serialized by json_encode.
     */
    public function jsonSerialize()
    {
        return $this->config;
    }

    /**
     * Decode a JSON encoded configuration and return the correct configuration
     * class (A or B) depending on the values set in the configuration.
     * @param string|array|stdClass The JSON encoded configuration (string), or the
     *                              already decoded JSON (array or stdClass).
     * @return ConfigTypeA|ConfigTypeB
     */
    public static function jsonDecode($config)
    {
        $config = static::convertConfigToArray($config);
        if (array_key_exists(ConfigTypeA::JSON_ASHOST, $config)
            || array_key_exists(ConfigTypeA::JSON_SYSNR, $config)
            || array_key_exists(ConfigTypeA::JSON_GWHOST, $config)
            || array_key_exists(ConfigTypeA::JSON_GWSERV, $config)
        ) {
            return new ConfigTypeA($config);
        }
        if (array_key_exists(ConfigTypeB::JSON_MSHOST, $config)
            || array_key_exists(ConfigTypeB::JSON_R3NAME, $config)
            || array_key_exists(ConfigTypeB::JSON_GROUP, $config)
        ) {
            return new ConfigTypeB($config);
        }
        throw new InvalidArgumentException(
            'Cannot automatically determine the configuration type from the'
            . ' given configuration keys!'
        );
    }

    /**
     * Convert any given configuration (array, JSON encoded string, or stdClass) to
     * an array for processing.
     * @param stdClass|array|string $config
     * @return array
     */
    private static function convertConfigToArray($config)
    {
        if (is_object($config) && $config instanceof stdClass) {
            $config = json_encode($config);
        }
        if (is_string($config)) {
            $config = json_decode($config, true);
        }
        if (!is_array($config)) {
            throw new InvalidArgumentException(
                'Expected configuration to be a JSON encoded array!'
            );
        }
        return $config;
    }
}
