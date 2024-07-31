<?php

declare(strict_types=1);

namespace phpsap\classes\Api\Traits;

use phpsap\exceptions\InvalidArgumentException;

/**
 * Trait OptionalTrait
 */
trait OptionalTrait
{
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
     * Set the API value optional flag.
     * @param bool $isOptional
     * @throws InvalidArgumentException
     */
    protected function setOptional(bool $isOptional): void
    {
        $this->set(self::JSON_OPTIONAL, $isOptional);
    }
}
