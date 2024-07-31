<?php

declare(strict_types=1);

namespace phpsap\classes\Api\Traits;

use phpsap\exceptions\InvalidArgumentException;

/**
 * Trait DirectionTrait
 */
trait DirectionTrait
{
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
     * Set the API value direction: input, output or table.
     * @param string $direction
     * @throws InvalidArgumentException
     */
    protected function setDirection(string $direction): void
    {
        if (!in_array($direction, $this->getAllowedDirections(), true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected API %s direction to be in: %s!',
                    static::class,
                    implode(', ', $this->getAllowedDirections())
                )
            );
        }
        $this->set(self::JSON_DIRECTION, $direction);
    }

    /**
     * Get an array of allowed directions.
     * @return array<int, string>
     */
    abstract protected function getAllowedDirections(): array;
}
