<?php

namespace tests\phpsap\classes\helper;

use phpsap\classes\AbstractConnection;
use phpsap\exceptions\ConnectionFailedException;
use phpsap\exceptions\UnknownFunctionException;

/**
 * Class tests\phpsap\classes\helper\Connection
 *
 * Helper class extending the abstract connection class for testing.
 *
 * @package tests\phpsap\classes\helper
 * @author  Gregor J.
 * @license MIT
 */
class Connection extends AbstractConnection
{
    /**
     * Prepare a remote function call and return a function instance.
     * @param string $name
     * @return \tests\phpsap\classes\helper\RemoteFunction
     * @throws ConnectionFailedException
     * @throws UnknownFunctionException
     */
    protected function createFunctionInstance($name)
    {
        return new RemoteFunction('FAKE CONNECTION RESOURCE', $name);
    }
}
