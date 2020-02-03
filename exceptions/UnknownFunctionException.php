<?php

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\IUnknownFunctionException;

/**
 * Class UnknownFunctionException
 *
 * The requested remote function could not be found.
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class UnknownFunctionException extends SapLogicException implements IUnknownFunctionException
{
}
