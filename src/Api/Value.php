<?php

namespace phpsap\classes\Api;

use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IValue;

/**
 * Class Value
 *
 * API values extend the logic of an element but have a direction (input or output)
 * and an optional flag, unlike elements.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class Value extends Element implements IValue
{
    /**
     * @var array Allowed JsonSerializable keys to set values for.
     */
    protected static $allowedKeys = [
        self::JSON_TYPE,
        self::JSON_NAME,
        self::JSON_DIRECTION,
        self::JSON_OPTIONAL
    ];

    /**
     * @var array List of allowed API value directions.
     */
    protected static $allowedDirections = [
        self::DIRECTION_INPUT,
        self::DIRECTION_OUTPUT
    ];

    /**
     * API value constructor.
     * @param string $type       Either string, int, float, bool or array
     * @param string $name       API value name.
     * @param string $direction  Either input, output or table.
     * @param bool   $isOptional Is the API value optional?
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function __construct($type, $name, $direction, $isOptional)
    {
        parent::__construct($type, $name);
        $this->setDirection($direction);
        $this->setOptional($isOptional);
    }

    /**
     * Get the direction of the API value.
     * @return string
     */
    public function getDirection()
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_DIRECTION);
    }

    /**
     * Is the value optional?
     * @return bool
     */
    public function isOptional()
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_OPTIONAL);
    }

    /**
     * Set the API value direction: input, output or table.
     * @param string $direction
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    protected function setDirection($direction)
    {
        if (!is_string($direction)) {
            throw new InvalidArgumentException(
                'Expected API value direction to be string!'
            );
        }
        if (!in_array($direction, static::$allowedDirections, true)) {
            throw new InvalidArgumentException(sprintf(
                'Expected API value direction to be in: %s!',
                implode(', ', static::$allowedDirections)
            ));
        }
        $this->set(self::JSON_DIRECTION, $direction);
    }

    /**
     * Set the API value optional flag.
     * @param bool $isOptional
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    protected function setOptional($isOptional)
    {
        if (!is_bool($isOptional)) {
            throw new InvalidArgumentException(
                'Expected API value isOptional flag to be boolean!'
            );
        }
        $this->set(self::JSON_OPTIONAL, $isOptional);
    }

    /**
     * Create an instance of this class from an array.
     * @param array $array Array containing the properties of this class.
     * @return \phpsap\classes\Api\Value
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public static function fromArray($array)
    {
        static::fromArrayValidation($array);
        return new self(
            $array[self::JSON_TYPE],
            $array[self::JSON_NAME],
            $array[self::JSON_DIRECTION],
            $array[self::JSON_OPTIONAL]
        );
    }
}
