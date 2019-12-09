<?php

namespace tests\phpsap\classes\helper;

use phpsap\classes\AbstractFunction;
use phpsap\classes\Api\RemoteApi;

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
     * @var array Fake extractApi() response.
     */
    public static $fakeApi = [];

    /**
     * @var array Fake invoke() response.
     */
    public static $fakeInvoke = [];

    /**
     * @inheritDoc
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function extractApi()
    {
        return new RemoteApi(self::$fakeApi);
    }

    /**
     * @inheritDoc
     */
    public function invoke()
    {
        return self::$fakeInvoke;
    }
}
