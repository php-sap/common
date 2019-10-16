<?php

namespace phpsap\classes\Api;

use phpsap\interfaces\Api\IValue;

/**
 * Class phpsap\classes\Api\Value
 *
 * API values have a direction (input, output or table) and an optional flag, unlike
 * elements.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class Value extends Element implements IValue
{
    /**
     * @var array List of allowed API value directions.
     */
    protected static $allowedDirections = [
        self::DIRECTION_INPUT,
        self::DIRECTION_OUTPUT
    ];

    /**
     * API value constructor.
     * @param string  $type        Either string, int, float, bool or array
     * @param string  $name        API value name.
     * @param string  $direction   Either input, output or table.
     * @param bool    $isOptional  Is the API value optional?
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
        return $this->data['direction'];
    }

    /**
     * Is the value optional?
     * @return bool
     */
    public function isOptional()
    {
        return $this->data['isOptional'];
    }

    /**
     * Set the API value direction: input, output or table.
     * @param string $direction
     * @throws \InvalidArgumentException
     */
    protected function setDirection($direction)
    {
        if (!is_string($direction)) {
            throw new \InvalidArgumentException(
                'Expected API value direction to be string!'
            );
        }
        if (!in_array($direction, static::$allowedDirections, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected API value direction to be in: %s!',
                implode(', ', static::$allowedDirections)
            ));
        }
        $this->data['direction'] = $direction;
    }

    /**
     * Set the API value optional flag.
     * @param bool $isOptional
     * @throws \InvalidArgumentException
     */
    protected function setOptional($isOptional)
    {
        if (!is_bool($isOptional)) {
            throw new \InvalidArgumentException(
                'Expected API value isOptional flag to be boolean!'
            );
        }
        $this->data['isOptional'] = $isOptional;
    }
}
