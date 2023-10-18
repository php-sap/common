<?php

namespace tests\phpsap\classes\helper;

use phpsap\classes\Util\JsonSerializable;
use phpsap\interfaces\Util\IJsonSerializable;

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
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function reset()
    {
        parent::reset();
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function has($key): bool
    {
        return parent::has($key);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function get($key)
    {
        return parent::get($key);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function set($key, $value)
    {
        parent::set($key, $value);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function setMultiple($data)
    {
        parent::setMultiple($data);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function remove($key)
    {
        parent::remove($key);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public static function jsonToArray($json): ?array
    {
        return parent::jsonToArray($json);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public static function objToArray($obj): ?array
    {
        return parent::objToArray($obj);
    }

    /**
     * @inheritDoc
     * @return $this
     */
    public static function jsonDecode($json): IJsonSerializable
    {
        return parent::jsonDecode($json);
    }
}
