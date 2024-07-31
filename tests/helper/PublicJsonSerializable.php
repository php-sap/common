<?php

declare(strict_types=1);

namespace tests\phpsap\classes\helper;

use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Util\IJsonSerializable;
use stdClass;

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
    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function reset(): void
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
    public function get(string $key): float|int|bool|array|string|null
    {
        return parent::get($key);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function set(string $key, float|int|bool|array|string|null $value): void
    {
        parent::set($key, $value);
    }

    /**
     * @inheritDoc
     * @noinspection PhpOverridingMethodVisibilityInspection
     */
    public function remove(string $key): void
    {
        parent::remove($key);
    }

    /**
     * @inheritDoc
     */
    public static function jsonDecode(string $json): IJsonSerializable
    {
        return parent::jsonDecode($json);
    }

    /**
     * @var array<int, string>
     */
    public static array $allowedKeys = ['variable'];

    /**
     * @inheritDoc
     */
    protected function getAllowedKeys(): array
    {
        return self::$allowedKeys;
    }

    /**
     * @param float|bool|int|string|array<int|string, mixed>|null $value
     * @return void
     * @throws InvalidArgumentException
     */
    protected function setVariable(array|float|bool|int|string|null $value): void
    {
        $this->set('variable', $value);
    }
}
