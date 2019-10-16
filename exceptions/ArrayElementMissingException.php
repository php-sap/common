<?php
/**
 * File exceptions/ArrayElementMissingException.php
 *
 * PHP/SAP function not found.
 *
 * @package exceptions
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\IArrayElementMissingException;

/**
 * Class phpsap\exceptions\ArrayElementMissingException
 *
 * Exception thrown when a SAP remote function cannot be found.
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class ArrayElementMissingException extends SapException implements IArrayElementMissingException
{
}
