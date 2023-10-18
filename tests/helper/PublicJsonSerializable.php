<?php

namespace tests\phpsap\classes\helper;

use phpsap\classes\Util\JsonSerializable;

/**
 * Class PublicJsonSerializable
 *
 * Tester class making protected methods of JsonSerializable public.
 *
 * @package tests\phpsap\classes\helper
 * @author  Gregor J.
 * @license MIT
 */
class PublicJsonSerializable extends JsonSerializable
{
    public static array $allowedKeys = [];

    /**
     * @inheritDoc
     */
    public function reset()
    {
        parent::reset();
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        return parent::has($key);
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        return parent::get($key);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value)
    {
        return parent::set($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($data)
    {
        parent::setMultiple($data);
    }

    /**
     * @inheritDoc
     */
    public function remove($key)
    {
        parent::remove($key);
    }

    /**
     * @inheritDoc
     */
    public static function jsonToArray($json): ?array
    {
        return parent::jsonToArray($json);
    }

    /**
     * @inheritDoc
     */
    public static function objToArray($obj): ?array
    {
        return parent::objToArray($obj);
    }

    /**
     * @inheritDoc
     * @return $this
     */
    public static function jsonDecode($json): \phpsap\interfaces\Util\IJsonSerializable
    {
        return parent::jsonDecode($json);
    }
}
