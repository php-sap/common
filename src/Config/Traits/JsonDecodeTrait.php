<?php

declare(strict_types=1);

namespace phpsap\classes\Config\Traits;

use JsonException;
use phpsap\classes\Config\ConfigTypeA;
use phpsap\classes\Config\ConfigTypeB;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Config\IConfigTypeA;
use phpsap\interfaces\Config\IConfigTypeB;
use phpsap\interfaces\Config\IConfiguration;

/**
 * Trait JsonDecodeTrait
 */
trait JsonDecodeTrait
{
    /**
     * Decode a JSON encoded configuration and return the correct configuration
     * class (A or B) depending on the values set in the configuration.
     * @param string $json JSON encoded configuration.
     * @return IConfiguration
     * @throws InvalidArgumentException
     */
    public static function jsonDecode(string $json): IConfiguration
    {
        try {
            $config = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            /**
             * JSON might decode into anything but array without an error.
             */
            if (!is_array($config)) {
                throw new InvalidArgumentException('JSON did not decode into an array!');
            }
        } catch (InvalidArgumentException | JsonException $exception) {
            throw new InvalidArgumentException(
                sprintf('Invalid JSON: Expected JSON encoded %s!', static::class),
                0,
                $exception
            );
        }
        if (
            array_key_exists(IConfigTypeA::JSON_ASHOST, $config)
            || array_key_exists(IConfigTypeA::JSON_SYSNR, $config)
            || array_key_exists(IConfigTypeA::JSON_GWHOST, $config)
            || array_key_exists(IConfigTypeA::JSON_GWSERV, $config)
        ) {
            return new ConfigTypeA($config);
        }
        if (
            array_key_exists(IConfigTypeB::JSON_MSHOST, $config)
            || array_key_exists(IConfigTypeB::JSON_R3NAME, $config)
            || array_key_exists(IConfigTypeB::JSON_GROUP, $config)
        ) {
            return new ConfigTypeB($config);
        }
        throw new InvalidArgumentException(
            'Cannot automatically determine the configuration type from the'
            . ' given configuration keys!'
        );
    }
}
