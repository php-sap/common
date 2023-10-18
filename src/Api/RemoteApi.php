<?php

namespace phpsap\classes\Api;

use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IValue;
use phpsap\interfaces\Api\IApi;
use phpsap\interfaces\Util\IJsonSerializable;

/**
 * Class RemoteApi
 *
 * This class contains the description of a single SAP remote function API.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class RemoteApi implements IApi
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * Add an input value of the remote function.
     * @param IValue $value
     * @return $this
     */
    public function add(IValue $value): IApi
    {
        $this->data[] = $value;
        return $this;
    }

    /**
     * Get all input values of the remote function.
     * @return Value[]
     */
    public function getInputValues(): array
    {
        return $this->getValues(Value::DIRECTION_INPUT);
    }

    /**
     * Get all output values of the remote function.
     * @return Value[]
     */
    public function getOutputValues(): array
    {
        return $this->getValues(Value::DIRECTION_OUTPUT);
    }

    /**
     * Get all tables of the remote function.
     * @return Table[]
     */
    public function getTables(): array
    {
        return $this->getValues(Table::DIRECTION_TABLE);
    }

    /**
     * Get the API values according to their direction: input, output, table.
     * @param string $direction
     * @return array
     */
    protected function getValues($direction): array
    {
        $result = [];
        foreach ($this->data as $value) {
            /**
             * @var Value $value
             */
            if ($value->getDirection() === $direction) {
                $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * Create a remote API from a given array.
     * @param array|null $values Array of remote API elements. Default: null
     * @throws InvalidArgumentException
     */
    public function __construct($values = null)
    {
        if ($values === null) {
            $values = [];
        }
        if (!is_array($values)) {
            throw new InvalidArgumentException('Expected array of API values.');
        }
        foreach ($values as $value) {
            /**
             * Call type-specific constructors from the array.
             */
            $this->data[] = $this->constructValue($value);
        }
    }

    /**
     * Construct an API value (IValue) from a given array.
     * @param array $value
     * @return IValue
     * @throws InvalidArgumentException
     */
    private function constructValue($value): IValue
    {
        if (!array_key_exists(Value::JSON_TYPE, $value)) {
            throw new InvalidArgumentException('API Value is missing type.');
        }
        if ($value[Value::JSON_TYPE] === Table::TYPE_TABLE) {
            return Table::fromArray($value);
        }
        if ($value[Value::JSON_TYPE] === Struct::TYPE_STRUCT) {
            return Struct::fromArray($value);
        }
        return Value::fromArray($value);
    }

    /**
     * Decode a formerly JSON encoded IApi object.
     * @param string $json JSON encoded remote API object.
     * @return RemoteApi
     * @throws InvalidArgumentException
     */
    public static function jsonDecode($json): IJsonSerializable
    {
        if (is_string($json)) {
            $array = json_decode($json, true);
            if (is_array($array)) {
                try {
                    return new self($array);
                } catch (InvalidArgumentException $exception) {
                    throw new InvalidArgumentException(sprintf(
                        'Invalid JSON! %s',
                        $exception->getMessage()
                    ));
                }
            }
        }
        throw new InvalidArgumentException(sprintf(
            'Invalid JSON! Expected JSON encoded %s string!',
            static::class
        ));
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}
