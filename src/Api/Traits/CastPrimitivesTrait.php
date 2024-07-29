<?php

declare(strict_types=1);

namespace phpsap\classes\Api\Traits;

use DateInterval;
use DateTime;
use phpsap\DateTime\SapDateInterval;
use phpsap\DateTime\SapDateTime;

/**
 * Trait CastPrimitivesTrait
 */
trait CastPrimitivesTrait
{
    /**
     * @inheritDoc
     */
    public function cast(null|float|bool|int|string $value): null|bool|int|float|string|DateTime|DateInterval
    {
        static $methods;
        if ($methods === null) {
            $methods = [
                self::TYPE_DATE      => static function ($value) {
                    /**
                     * In case the date value consists only of zeros, this
                     * is most likely a mistake of the SAP remote function.
                     */
                    if (preg_match('~^0+$~', $value)) {
                        return null;
                    }
                    return SapDateTime::createFromFormat(SapDateTime::SAP_DATE, $value);
                },
                self::TYPE_TIME      => static function ($value) {
                    return SapDateInterval::createFromDateString($value);
                },
                self::TYPE_TIMESTAMP => static function ($value) {
                    return SapDateTime::createFromFormat(SapDateTime::SAP_TIMESTAMP, $value);
                },
                self::TYPE_WEEK      => static function ($value) {
                    return SapDateTime::createFromFormat(SapDateTime::SAP_WEEK, $value);
                },
                self::TYPE_HEXBIN    => static function ($value) {
                    return hex2bin(trim($value));
                }
            ];
        }
        $type = $this->getType();
        if (array_key_exists($type, $methods)) {
            $method = $methods[$type];
            return $method($value);
        }
        settype($value, $type);
        return $value;
    }
}
