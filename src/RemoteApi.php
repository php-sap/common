<?php

namespace phpsap\classes;

use InvalidArgumentException;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\exceptions\ArrayElementMissingException;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IValue;
use phpsap\interfaces\IApi;

/**
 * Class phpsap\classes\RemoteApi
 *
 * This class contains the description of a single SAP remote function API.
 *
 * @package phpsap\classes
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
     * @return \phpsap\classes\RemoteApi
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
     * @return \phpsap\interfaces\Api\IArray[]
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
     */
    public function castTables($array)
    {
        return $this->castValues(IArray::DIRECTION_TABLE, $array);
    }

    /**
     * Type cast the given array according to the input/output/table values of the
     * API.
     * @param string $direction The direction to get the casting values for.
     * @param array $array The data array to cast.
     * @return array
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
             * @var \phpsap\interfaces\Api\IValue $value
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
     * Decode a formerly JSON encoded IApi object.
     * @param string|\stdClass|array|null $values Either a JSON encoded remote API
     *                                            as array, object or string, or
     *                                            null for an empty API description.
     */
    public function __construct($values = null)
    {
        if ($values === null) {
            $values = [];
        }
        if (is_string($values)) {
            $values = json_decode($values, true);
        }
        if (!is_array($values)) {
            throw new InvalidArgumentException('Invalid JSON: values are not in an array!');
        }
        foreach ($values as $value) {
            /**
             * In case the value is an object, convert it to an array.
             */
            if (is_object($value)) {
                $value = json_decode(json_encode($value), true);
            }
            if (!is_array($value)) {
                throw new InvalidArgumentException('Invalid JSON: value is not an array!');
            }
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
     */
    private function constructValue($value)
    {
        if (!array_key_exists(IValue::JSON_TYPE, $value)) {
            throw new InvalidArgumentException('Invalid JSON: missing type!');
        }
        if ($value[IValue::JSON_TYPE] === IArray::TYPE_ARRAY) {
            return $this->constructArray($value);
        }
        return Value::jsonDecode($value);
    }

    /**
     * Construct an array type element (table or struct) from the given JSON array.
     * @param array $value
     * @return \phpsap\interfaces\Api\IArray
     */
    private function constructArray($value)
    {
        if (!array_key_exists(IValue::JSON_DIRECTION, $value)) {
            throw new InvalidArgumentException('Invalid JSON: missing direction!');
        }
        if ($value[IValue::JSON_DIRECTION] === IArray::DIRECTION_TABLE) {
            return Table::jsonDecode($value);
        }
        return Struct::jsonDecode($value);
    }

    /**
     * Decode a formerly JSON encoded IApi object.
     * @param string|\stdClass|array $json
     * @return \phpsap\classes\RemoteApi
     */
    public static function jsonDecode($json)
    {
        return new self($json);
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
        return $this->data;
    }
}
