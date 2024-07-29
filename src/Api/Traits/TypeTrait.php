<?php

declare(strict_types=1);

namespace phpsap\classes\Api\Traits;

use phpsap\exceptions\InvalidArgumentException;

/**
 * Trait TypeTrait
 */
trait TypeTrait
{
    /**
     * The PHP type of the element.
     * @return string
     * @throws InvalidArgumentException
     */
    public function getType(): string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_TYPE);
    }

    /**
     * Set optional the API element PHP type.
     * @param string $type
     * @throws InvalidArgumentException
     */
    private function setType(string $type): void
    {
        if (!in_array($type, $this->getAllowedTypes(), true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected API %s type to be in: %s!',
                    static::class,
                    implode(', ', $this->getAllowedTypes())
                )
            );
        }
        $this->set(self::JSON_TYPE, $type);
    }

    /**
     * Get an array of allowed types.
     * @return array<int, string>
     */
    abstract private function getAllowedTypes(): array;
}
