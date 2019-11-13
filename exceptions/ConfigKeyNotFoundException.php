<?php
/**
 * File exceptions/ConfigKeyNotFoundExceptionException.php
 *
 * No entry was found in the container.
 *
 * @package common
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\exceptions;

use phpsap\interfaces\exceptions\IConfigKeyNotFoundException;

/**
 * Class phpsap\exceptions\ConfigKeyNotFoundException
 *
 * The configuration key doesn't exist for the current SAP configuration (A or B).
 *
 * @package phpsap\exceptions
 * @author  Gregor J.
 * @license MIT
 */
class ConfigKeyNotFoundException extends SapException implements IConfigKeyNotFoundException
{
}
