<?php

declare(strict_types=1);

namespace phpsap\classes\Api;

use phpsap\DateTime\SapDateInterval;
use phpsap\DateTime\SapDateTime;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IValue;

/**
 * Class Value
 *
 * API values extend the logic of an element but have a direction (input or output)
 * and an optional flag, unlike elements.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class Value extends Element implements IValue
{
    /**
     * @var array List of allowed API value types.
     */
    protected static array $allowedTypes = [
        self::TYPE_BOOLEAN,
        self::TYPE_INTEGER,
        self::TYPE_FLOAT,
        self::TYPE_STRING,
        self::TYPE_HEXBIN,
        self::TYPE_DATE,
        self::TYPE_TIME,
        self::TYPE_TIMESTAMP,
        self::TYPE_WEEK
    ];

    /**
     * Cast a given output value to the type defined in this class.
     * @param bool|int|float|string $value The output to typecast.
     * @return bool|int|float|string|SapDateTime|SapDateInterval
     * @throws InvalidArgumentException
     */
    public function cast(bool|int|float|string $value): bool|int|float|string|SapDateTime|SapDateInterval
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

    /**
     * Create an instance of this class from an array.
     * @param array $array Array containing the properties of this class.
     * @return Value
     * @throws InvalidArgumentException
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function fromArray(array $array): Value
    {
        static::fromArrayValidation($array);
        return new self(
            $array[self::JSON_TYPE],
            $array[self::JSON_NAME],
            $array[self::JSON_DIRECTION],
            $array[self::JSON_OPTIONAL]
        );
    }
}
