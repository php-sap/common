<?php

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
     * @var array Valid config keys for testing.
     */
    public static $allowedKeys = ['zadgcjmt'];

    /**
     * Retrieves a configuration value for a given key.
     * @param string $key
     * @return string|int
     */
    public function get($key)
    {
        return parent::get($key);
    }

    /**
     * Sets a configuration value for a given key.
     * @param string     $key
     * @param string|int $value
     * @throws LogicException In case of an invalid configuration key.
     * @throws InvalidArgumentException In case of an invalid configuration value.
     */
    public function set($key, $value)
    {
        return parent::set($key, $value);
    }

    /**
     * Removes a configuration value for a given key.
     * @param string $key
     */
    public function remove($key)
    {
        return parent::remove($key);
    }

    /**
     * Returns true if the configuration has a value for the given key.
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return parent::has($key);
    }

    /**
     * Test set() function.
     * @param $value
     * @return $this
     */
    public function setZadgcjmt($value)
    {
        $this->set('zadgcjmt', $value);
        return $this;
    }
}
