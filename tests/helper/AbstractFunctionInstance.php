<?php

declare(strict_types=1);

namespace tests\phpsap\classes\helper;

use phpsap\classes\AbstractFunction;
use phpsap\classes\Api\RemoteApi;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IApi;

/**
 * Class AbstractFunctionInstance
 *
 * AbstractFunction instance.
 *
 * @package tests\phpsap\classes\helper
 * @author  Gregor J.
 * @license MIT
 */
class AbstractFunctionInstance extends AbstractFunction
{
    /**
     * Fake extractApi() response.
     * @var array<int, array<string, array<int, array<string, string>>|bool|string>>
     */
    public static array $fakeApi = [];

    /**
     * @var array<string, mixed> Fake invoke() response.
     */
    public static array $fakeInvoke = [];

    /**
     * @inheritDoc
     * @throws InvalidArgumentException
     */
    public function extractApi(): IApi
    {
        return new RemoteApi(self::$fakeApi);
    }

    /**
     * @inheritDoc
     */
    public function invoke(): array
    {
        return self::$fakeInvoke;
    }
}
