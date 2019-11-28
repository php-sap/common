<?php

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\IConnectionFailedException;

/**
 * Class ConnectionFailedException
 *
 * The SAP connection failed.
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class ConnectionFailedException extends SapRuntimeException implements IConnectionFailedException
{
}
