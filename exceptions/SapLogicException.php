<?php

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\ISapException;

/**
 * Class phpsap\exceptions\SapLogicException
 *
 * Generic SAP exception that represents error in the program logic. This kind of
 * exception should lead directly to a fix in your code. A user should not be
 * bothered with the details of this exception.
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class SapLogicException extends \LogicException implements ISapException
{
}
