<?php

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\IArrayElementMissingException;

/**
 * Class ArrayElementMissingException
 *
 * A table element required by the API is not in the table.
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class ArrayElementMissingException extends SapLogicException implements IArrayElementMissingException
{
}
