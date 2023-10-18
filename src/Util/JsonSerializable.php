<?php

namespace phpsap\classes\Util;

use stdClass;
use phpsap\interfaces\Util\IJsonSerializable;
use phpsap\exceptions\InvalidArgumentException;

/**
 * Class JsonSerializable
 *
 * Class implementing IJsonSerializable for simple values (bool, int, float, string,
 * array) and adding basic functionality to set and retrieve these values.
 *
 * @package phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
class JsonSerializable implements IJsonSerializable
{
    /**
     * @var stdClass
     */
    private $data;

    /**
     * @var array Allowed data types for values.
     */
    protected static $allowedDataTypes = [
        'integer',
        'string',
        'boolean',
        'double',
        'float',
        'array'
    ];

    /**
     * @var array Allowed keys to set values for.
     */
    protected static $allowedKeys = [];

    /**
     * Get an array of all valid keys this class is able to set().
     * @return array
     */
    protected function getAllowedKeys()
    {
        return static::$allowedKeys;
    }

    /**
     * Get an array of all valid PHP data types allowed for the stored values.
     * @return array
     */
    protected function getAllowedDataTypes()
    {
        return static::$allowedDataTypes;
    }

    /**
     * Initializing the data.
     * @param array|null $data Associative array of keys and values to set.
     * @throws InvalidArgumentException
     */
    public function __construct($data = null)
    {
        $this->reset();
        if ($data !== null) {
            $this->setMultiple($data);
        }
    }

    /**
     * Remove all keys and values from the data.
     */
    protected function reset()
    {
        $this->data = new stdClass();
    }

    /**
     * Determine whether the data contains the given key.
     * @param string $key The key to look for.
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function has($key)
    {
        return property_exists($this->data, $this->validateKey($key));
    }

    /**
     * Get the value of the given key from the data.
     * @param string $key The key to retrieve from the data.
     * @return null|bool|int|float|string|array The value of the key, or null in case the key didn't exist.
     * @throws InvalidArgumentException
     */
    protected function get($key)
    {
        if ($this->has($key)) {
            return $this->data->{$key};
        }
        return null;
    }

    /**
     * Set the given key to the given value in the data.
     * @param string                      $key    The key to set the value for.
     * @param bool|int|float|string|array $value  The value to set.
     * @throws InvalidArgumentException
     */
    protected function set($key, $value)
    {
        if ($value === null) {
            $this->remove($key);
            return;
        }
        if (!in_array($this->validateKey($key), $this->getAllowedKeys(), true)) {
            throw new InvalidArgumentException(sprintf('Unknown key \'%s\'!', $key));
        }
        $this->setValue($key, $value);
    }

    /**
     * This method extracts only allowed keys from the given array. This way it
     * behaves differently than the set() method. This method will never throw an
     * exception because of an invalid key.
     * @param array $data Associative array of keys and values to set.
     * @throws InvalidArgumentException
     */
    protected function setMultiple($data)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid array! Expected data to be an array, but got %s!',
                gettype($data)
            ));
        }
        foreach ($this->getAllowedKeys() as $key) {
            if (array_key_exists($key, $data)) {
                $this->setValue($key, $data[$key]);
            }
        }
    }

    /**
     * Set the value for a valid and allowed key. This method will not check the key
     * anymore, only the value!
     * @param string $key The key to set the value for.
     * @param mixed $value The value to set.
     * @throws InvalidArgumentException
     */
    private function setValue($key, $value)
    {
        if (!in_array(gettype($value), $this->getAllowedDataTypes(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid value! Expected a simple value (\'%s\'), but got \'%s\'!',
                implode('\', \'', $this->getAllowedDataTypes()),
                gettype($value)
            ));
        }
        $this->data->{$key} = $value;
    }

    /**
     * Remove a given key from the data.
     * @param string $key The key to remove.
     * @throws InvalidArgumentException
     */
    protected function remove($key)
    {
        if ($this->has($key)) {
            unset($this->data->{$key});
        }
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * Export the data of this class as array.
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this->data);
    }

    /**
     * Validate key names.
     * Only strings containing alphanumeric characters are allowed as keys.
     * @param mixed $key
     * @return string
     * @throws InvalidArgumentException
     */
    private function validateKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid key! Expected key to be string, but got %s!',
                gettype($key)
            ));
        }
        if (!preg_match('~^[a-z\d_\-]+$~i', $key, $matches)) {
            throw new InvalidArgumentException(
                'Invalid key! Key must contain only alphanumeric characters!'
            );
        }
        return $matches[0];
    }

    /**
     * Decode a formerly JSON encoded object.
     * @param string $json JSON encoded object.
     * @return $this
     * @throws InvalidArgumentException
     */
    public static function jsonDecode($json): IJsonSerializable
    {
        $array = self::jsonToArray($json);
        return new static($array);
    }

    /**
     * Decode a JSON encoded object to an array.
     * @param string $json JSON encoded object.
     * @return array|null Array of the JSON encoded object or null, in case there
     *                    was an error.
     * @throws InvalidArgumentException
     */
    protected static function jsonToArray($json)
    {
        if (is_string($json)) {
            $array = json_decode($json, true);
            if (is_array($array)) {
                return $array;
            }
        }
        throw new InvalidArgumentException(sprintf(
            'Invalid JSON! Expected JSON encoded %s string!',
            static::class
        ));
    }

    /**
     * Convert any given representation of a JSON object to an array.
     * @param stdClass|array|string $obj  JSON encoded object (string), or a JSON
     *                                     decoded object (stdClass or array).
     * @return array|null
     * @throws InvalidArgumentException
     */
    protected static function objToArray($obj)
    {
        if (is_object($obj)) {
            $obj = json_encode($obj);
        }
        if (is_string($obj)) {
            $obj = json_decode($obj, true);
        }
        if (is_array($obj)) {
            return $obj;
        }
        throw new InvalidArgumentException(sprintf(
            'Invalid JSON object! Expected %s JSON object or array!',
            static::class
        ));
    }
}
