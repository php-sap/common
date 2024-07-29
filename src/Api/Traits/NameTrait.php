<?php

declare(strict_types=1);

namespace phpsap\classes\Api\Traits;

use phpsap\exceptions\InvalidArgumentException;

/**
 * Trait NameTrait
 */
trait NameTrait
{
    /**
     * The name of the element.
     * @return string
     * @throws InvalidArgumentException
     */
    public function getName(): string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_NAME);
    }

    /**
     * Set the API element name.
     * @param string $name
     * @throws InvalidArgumentException
     */
    private function setName(string $name): void
    {
        if (trim($name) === '') {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected API %s name to be string!',
                    static::class
                )
            );
        }
        $this->set(self::JSON_NAME, trim($name));
    }
}
