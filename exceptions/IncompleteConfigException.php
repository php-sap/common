<?php

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\IIncompleteConfigException;

/**
 * Class IncompleteConfigException
 *
 * The configuration is incomplete.
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class IncompleteConfigException extends SapLogicException implements IIncompleteConfigException
{
}
