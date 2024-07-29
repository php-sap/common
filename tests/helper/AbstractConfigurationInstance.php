<?php

declare(strict_types=1);

namespace tests\phpsap\classes\helper;

use phpsap\exceptions\InvalidArgumentException;
use phpsap\classes\Config\AbstractConfiguration;

/**
 * Class tests\phpsap\classes\helper\AbstractConfigurationInstance
 *
 * AbstractConfiguration instance where all protected methods are made public for
 * testing.
 *
 * @package tests\phpsap\classes\helper
 * @author  Gregor J.
 * @license MIT
 */
class AbstractConfigurationInstance extends AbstractConfiguration
{
    /**
     * @var array<int, string> Valid config keys for testing.
     */
    public static array $allowedKeys = ['zadgcjmt'];

    /**
     * Retrieves a configuration value for a given key.
     * @param string $key
     * @return null|bool|int|float|string|array<int|string, mixed>
     * @throws InvalidArgumentException
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function get(string $key): null|bool|int|float|string|array
    {
        return parent::get($key);
    }

    /**
     * Sets a configuration value for a given key.
     * @param string $key
     * @param null|bool|int|float|string|array<int|string, mixed> $value
     * @throws InvalidArgumentException In case of an invalid configuration key or value.
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function set(string $key, null|bool|int|float|string|array $value): void
    {
        parent::set($key, $value);
    }

    /**
     * Removes a configuration value for a given key.
     * @param string $key
     * @throws InvalidArgumentException
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function remove(string $key): void
    {
        parent::remove($key);
    }

    /**
     * Returns true if the configuration has a value for the given key.
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function has(string $key): bool
    {
        return parent::has($key);
    }

    /**
     * Test set() function.
     * @param string|int $value
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setZadgcjmt(string|int $value): AbstractConfigurationInstance
    {
        $this->set('zadgcjmt', $value);
        return $this;
    }
}
