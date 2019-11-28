<?php

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\IFunctionCallException;

/**
 * Class FunctionCallException
 *
 * The SAP remote function call failed.
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class FunctionCallException extends SapRuntimeException implements IFunctionCallException
{
}
