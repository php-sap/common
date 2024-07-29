<?php /** @noinspection PhpClassNamingConventionInspection */

declare(strict_types=1);

namespace phpsap\classes\Config;

use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Config\IConfiguration;
use stdClass;

/**
 * Class phpsap\classes\Config\AbstractConfiguration
 *
 * This class reads, writes and removes configuration keys and their values.
 *
 * @package phpsap\classes\Config
 * @author  Gregor J.
 * @license MIT
 */
abstract class AbstractConfiguration extends JsonSerializable implements IConfiguration
{
    /**
     * @var array Allowed data types for values.
     */
    protected static array $allowedDataTypes = [
        'integer',
        'string'
    ];

    /**
     * Load the configuration either from a JSON encoded string or from an array.
     * @param array|string|stdClass $config the configuration
     * @throws InvalidArgumentException In case the configuration is neither JSON
     *                                  nor an array.
     */
    public function __construct($config = null)
    {
        parent::__construct();
        if ($config === null) {
            return;
        }
        $config = static::objToArray($config);
        foreach ($this->getAllowedKeys() as $key) {
            if (array_key_exists($key, $config)) {
                $method = sprintf('set%s', ucfirst($key));
                $this->{$method}($config[$key]);
            }
        }
    }

    /**
     * Decode a JSON encoded configuration and return the correct configuration
     * class (A or B) depending on the values set in the configuration.
     * @param string $json JSON encoded configuration.
     * @return IConfiguration
     * @throws InvalidArgumentException
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function jsonDecode(string $json): IConfiguration
    {
        $config = static::jsonToArray($json);
        if (
            array_key_exists(ConfigTypeA::JSON_ASHOST, $config)
            || array_key_exists(ConfigTypeA::JSON_SYSNR, $config)
            || array_key_exists(ConfigTypeA::JSON_GWHOST, $config)
            || array_key_exists(ConfigTypeA::JSON_GWSERV, $config)
        ) {
            return new ConfigTypeA($config);
        }
        if (
            array_key_exists(ConfigTypeB::JSON_MSHOST, $config)
            || array_key_exists(ConfigTypeB::JSON_R3NAME, $config)
            || array_key_exists(ConfigTypeB::JSON_GROUP, $config)
        ) {
            return new ConfigTypeB($config);
        }
        throw new InvalidArgumentException(
            'Cannot automatically determine the configuration type from the'
            . ' given configuration keys!'
        );
    }
}
