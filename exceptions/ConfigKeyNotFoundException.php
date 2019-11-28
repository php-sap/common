<?php

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\IConfigKeyNotFoundException;

/**
 * Class ConfigKeyNotFoundException
 *
 * The configuration key doesn't exist for the current SAP configuration (A or B).
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class ConfigKeyNotFoundException extends SapLogicException implements IConfigKeyNotFoundException
{
}
