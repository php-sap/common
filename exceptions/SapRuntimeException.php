<?php

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\ISapException;

/**
 * Class phpsap\exceptions\SapRuntimeException
 *
 * Generic SAP exception thrown if an error which can only be found on runtime
 * occurs. A user can be bothered with the details of this exception.
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class SapRuntimeException extends \RuntimeException implements ISapException
{
}
