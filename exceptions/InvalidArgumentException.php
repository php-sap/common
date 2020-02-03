<?php

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\IInvalidArgumentException;

/**
 * Class phpsap\exceptions\InvalidArgumentException
 *
 * A given argument is of invalid type or the value is not according to the
 * expectations if the method.
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class InvalidArgumentException extends SapLogicException implements IInvalidArgumentException
{
}
