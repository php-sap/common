<?php

declare(strict_types=1);

namespace phpsap\classes\Util;

use JsonException;
use stdClass;
use phpsap\interfaces\Util\IJsonSerializable;
use phpsap\exceptions\InvalidArgumentException;
use TypeError;

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
abstract class JsonSerializable implements IJsonSerializable
{
    /**
     * @var stdClass
     */
    private stdClass $data;

    /**
     * Get an array of all valid keys this class is able to set().
     * @return array<int, string>
     */
    abstract protected function getAllowedKeys(): array;

    /**
     * Initializing the data.
     * @param array<string, null|bool|int|float|string|array<int|string, mixed>> $data Associative array of keys and values to set.
     * @throws InvalidArgumentException
     */
    public function __construct(array $data = [])
    {
        $this->reset();
        // get allowed keys from the array
        foreach ($this->getAllowedKeys() as $key) {
            if (array_key_exists($key, $data)) {
                //call config key specific set method
                $method = sprintf('set%s', ucfirst($key));
                try {
                    $this->{$method}($data[$key]);
                } catch (TypeError $error) {
                    throw new InvalidArgumentException($error->getMessage());
                }
            }
        }
    }

    /**
     * Remove all keys and values from the data.
     */
    protected function reset(): void
    {
        $this->data = new stdClass();
    }

    /**
     * Determine whether the data contains the given key.
     * @param string $key The key to look for.
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function has(string $key): bool
    {
        return property_exists($this->data, $this->validateKey($key));
    }

    /**
     * Get the value of the given key from the data.
     * @param string $key The key to retrieve from the data.
     * @return null|bool|int|float|string|array<int|string, mixed> The value of the key, or null in case the key didn't exist.
     * @throws InvalidArgumentException
     */
    protected function get(string $key): null|bool|int|float|string|array
    {
        if ($this->has($key)) {
            return $this->data->{$key};
        }
        return null;
    }

    /**
     * Set the given key to the given value in the data.
     * @param string $key    The key to set the value for.
     * @param null|bool|int|float|string|array<int|string, mixed> $value  The value to set.
     * @throws InvalidArgumentException
     */
    protected function set(string $key, null|bool|int|float|string|array $value): void
    {
        if ($value === null) {
            $this->remove($key);
            return;
        }
        if (!in_array($this->validateKey($key), $this->getAllowedKeys(), true)) {
            throw new InvalidArgumentException(sprintf('Unknown key \'%s\'!', $key));
        }
        $this->data->{$key} = $value;
    }

    /**
     * Remove a given key from the data.
     * @param string $key The key to remove.
     * @throws InvalidArgumentException
     */
    protected function remove(string $key): void
    {
        if ($this->has($key)) {
            unset($this->data->{$key});
        }
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->data;
    }

    /**
     * Export the data of this class as array.
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return get_object_vars($this->data);
    }

    /**
     * Validate key names.
     * Only strings containing alphanumeric characters are allowed as keys.
     * @param string $key
     * @return string
     * @throws InvalidArgumentException
     */
    private function validateKey(string $key): string
    {
        if (!preg_match('~^[a-z\d_\-]+$~i', $key, $matches)) {
            throw new InvalidArgumentException(
                'Invalid key! Key must contain only alphanumeric characters!'
            );
        }
        return $matches[0];
    }

    /**
     * Decode a formerly JSON encoded object.
     * @param string $json
     * @return IJsonSerializable
     * @throws InvalidArgumentException
     */
    public static function jsonDecode(string $json): IJsonSerializable
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
