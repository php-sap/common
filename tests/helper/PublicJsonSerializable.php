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
    public function has(string $key): bool
    {
        return parent::has($key);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function get(string $key)
    {
        return parent::get($key);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function set(string $key, $value)
    {
        parent::set($key, $value);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function setMultiple(array $data)
    {
        parent::setMultiple($data);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function remove(string $key)
    {
        parent::remove($key);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public static function jsonToArray(string $json): ?array
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
    public static function jsonDecode(string $json): IJsonSerializable
    {
        return parent::jsonDecode($json);
    }
}
