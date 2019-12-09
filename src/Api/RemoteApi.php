<?php

namespace phpsap\classes\Api;

use phpsap\exceptions\InvalidArgumentException;
use phpsap\exceptions\ArrayElementMissingException;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IValue;
use phpsap\interfaces\Api\IApi;

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
    private $data = [];

    /**
     * Add an input value of the remote function.
     * @param \phpsap\interfaces\Api\IValue $value
     * @return \phpsap\classes\Api\RemoteApi
     */
    public function add(IValue $value)
    {
        $this->data[] = $value;
        return $this;
    }

    /**
     * Get all input values of the remote function.
     * @return \phpsap\interfaces\Api\IValue[]
     */
    public function getInputValues()
    {
        return $this->getValues(IValue::DIRECTION_INPUT);
    }

    /**
     * Get all output values of the remote function.
     * @return \phpsap\interfaces\Api\IValue[]
     */
    public function getOutputValues()
    {
        return $this->getValues(IValue::DIRECTION_OUTPUT);
    }

    /**
     * Get all tables of the remote function.
     * @return \phpsap\classes\Api\Table[]
     */
    public function getTables()
    {
        return $this->getValues(IArray::DIRECTION_TABLE);
    }

    /**
     * Get the API values according to their direction: input, output, table.
     * @param string $direction
     * @return array
     */
    protected function getValues($direction)
    {
        $result = [];
        foreach ($this->data as $value) {
            /**
             * @var \phpsap\interfaces\Api\IValue $value
             */
            if ($value->getDirection() === $direction) {
                $result[] = $value;
            }
        }
        return $result;
    }

    /**
     * Typecast the values of a given array to their according types of the input
     * values of the remote function.
     * @param array $array The data array to cast.
     * @return array
     * @throws \phpsap\exceptions\ArrayElementMissingException
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function castInputValues($array)
    {
        return $this->castValues(IValue::DIRECTION_INPUT, $array);
    }

    /**
     * Typecast the values of a given array to their according types of the output
     * values of the remote function.
     * @param array $array The data array to cast.
     * @return array
     * @throws \phpsap\exceptions\ArrayElementMissingException
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function castOutputValues($array)
    {
        return $this->castValues(IValue::DIRECTION_OUTPUT, $array);
    }

    /**
     * Typecast the values of a given array to their according types of the table
     * values of the remote function.
     * @param array $array The data array to cast.
     * @return array
     * @throws \phpsap\exceptions\ArrayElementMissingException
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function castTables($array)
    {
        return $this->castValues(IArray::DIRECTION_TABLE, $array);
    }

    /**
     * Type cast the given array according to the input/output/table values of the
     * API.
     * @param string $direction The direction to get the casting values for.
     * @param array  $array     The data array to cast.
     * @return array
     * @throws \phpsap\exceptions\ArrayElementMissingException
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    protected function castValues($direction, $array)
    {
        if (!is_array($array)) {
            throw new InvalidArgumentException(
                'Expected data for typecasting to be an array!'
            );
        }
        $values = $this->getValues($direction);
        foreach ($values as $value) {
            /**
             * @var \phpsap\classes\Api\Value $value
             * Get the name of the current API value, so there isn't a function call
             * every time the name is needed.
             */
            $name = $value->getName();
            if (array_key_exists($name, $array)) {
                /**
                 * The API value exists and can be type casted.
                 */
                $array[$name] = $value->cast($array[$name]);
            } elseif ($value->isOptional() === false) {
                /**
                 * The API value doesn't exist, but is required.
                 */
                throw new ArrayElementMissingException(sprintf(
                    'Mandatory %s value %s is missing!',
                    $direction,
                    $name
                ));
            }
            /**
             * In case the API value doesn't exist, but isn't required, nothing
             * happens.
             */
        }
        return $array;
    }

    /**
     * Create a remote API from a given array.
     * @param array|null $values Array of remote API elements. Default: null
     * @throws \phpsap\exceptions\InvalidArgumentException
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
     * @return \phpsap\interfaces\Api\IValue
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    private function constructValue($value)
    {
        if (!array_key_exists(IValue::JSON_TYPE, $value)) {
            throw new InvalidArgumentException('API Value is missing type.');
        }
        if ($value[IValue::JSON_TYPE] === IArray::TYPE_ARRAY) {
            return $this->constructArray($value);
        }
        return Value::fromArray($value);
    }

    /**
     * Construct an array type element (table or struct) from the given JSON array.
     * @param array $value
     * @return \phpsap\interfaces\Api\IArray
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    private function constructArray($value)
    {
        if (!array_key_exists(IArray::JSON_DIRECTION, $value)) {
            throw new InvalidArgumentException('API Value is missing direction.');
        }
        if ($value[IArray::JSON_DIRECTION] === IArray::DIRECTION_TABLE) {
            return Table::fromArray($value);
        }
        return Struct::fromArray($value);
    }

    /**
     * Decode a formerly JSON encoded IApi object.
     * @param string $json JSON encoded remote API object.
     * @return \phpsap\classes\Api\RemoteApi
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public static function jsonDecode($json)
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
