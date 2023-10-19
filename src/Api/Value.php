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
    protected static array $allowedKeys = [
        self::JSON_TYPE,
        self::JSON_NAME,
        self::JSON_DIRECTION,
        self::JSON_OPTIONAL
    ];

    /**
     * @var array List of allowed API value directions.
     */
    protected static array $allowedDirections = [
        self::DIRECTION_INPUT,
        self::DIRECTION_OUTPUT
    ];

    /**
     * API value constructor.
     * @param string $type       Either string, int, float, bool or array
     * @param string $name       API value name.
     * @param string $direction  Either input, output or table.
     * @param bool $isOptional Is the API value optional?
     * @throws InvalidArgumentException
     */
    public function __construct(string $type, string $name, string $direction, bool $isOptional)
    {
        parent::__construct($type, $name);
        $this->setDirection($direction);
        $this->setOptional($isOptional);
    }

    /**
     * Get the direction of the API value.
     * @return string
     * @throws InvalidArgumentException
     */
    public function getDirection(): string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_DIRECTION);
    }

    /**
     * Is the value optional?
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isOptional(): bool
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_OPTIONAL);
    }

    /**
     * Set the API value direction: input, output or table.
     * @param string $direction
     * @throws InvalidArgumentException
     */
    protected function setDirection(string $direction)
    {
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
     * @throws InvalidArgumentException
     */
    protected function setOptional(bool $isOptional)
    {
        $this->set(self::JSON_OPTIONAL, $isOptional);
    }

    /**
     * Create an instance of this class from an array.
     * @param array $array Array containing the properties of this class.
     * @return Value
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $array): Value
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
