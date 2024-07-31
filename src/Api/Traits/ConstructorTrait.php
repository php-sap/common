<?php

declare(strict_types=1);

namespace phpsap\classes\Api\Traits;

use phpsap\exceptions\InvalidArgumentException;

/**
 * Trait ConstructorTrait
 */
trait ConstructorTrait
{
    /**
     * @inheritDoc
     */
    public function __construct(array $data = [])
    {
        /** @noinspection PhpMultipleClassDeclarationsInspection */
        parent::__construct($data);
        // all keys are required
        foreach ($this->getAllowedKeys() as $key) {
            if (!$this->has($key)) {
                throw new InvalidArgumentException(sprintf('%s is missing %s', static::class, $key));
            }
        }
    }
}
