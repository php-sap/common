<?php

namespace phpsap\classes\Api;

use phpsap\classes\Util\JsonSerializable;
use phpsap\DateTime\SapDateInterval;
use phpsap\DateTime\SapDateTime;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IElement;

/**
 * Class Element
 *
 * API elements are struct or table values and have no direction or optional flag of
 * their own.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class Element extends JsonSerializable implements IElement
{
    /**
     * @var array Allowed JsonSerializable keys to set values for.
     */
    protected static $allowedKeys = [
        self::JSON_TYPE,
        self::JSON_NAME
    ];

    /**
     * @var array List of allowed API element types.
     */
    protected static $allowedTypes = [
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
     * API element constructor.
     * @param string $type Either string, int, float, bool or array
     * @param string $name API element name.
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function __construct($type, $name)
    {
        parent::__construct();
        $this->setType($type);
        $this->setName($name);
    }

    /**
     * The PHP type of the element.
     * @return string
     */
    public function getType(): string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_TYPE);
    }

    /**
     * The name of the element.
     * @return string
     */
    public function getName(): string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_NAME);
    }

    /**
     * Set optional the API element PHP type.
     * @param string $type
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    protected function setType($type)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException(
                'Expected API element type to be string!'
            );
        }
        if (!in_array($type, static::$allowedTypes, true)) {
            throw new InvalidArgumentException(sprintf(
                'Expected API element type to be in: %s!',
                implode(', ', static::$allowedTypes)
            ));
        }
        $this->set(self::JSON_TYPE, $type);
    }

    /**
     * Set the API element name.
     * @param string $name
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    protected function setName($name)
    {
        if (!is_string($name) || $name === '') {
            throw new InvalidArgumentException(
                'Expected API element name to be string!'
            );
        }
        $this->set(self::JSON_NAME, $name);
    }

    /**
     * Cast a given output value to the type defined in this class.
     * @param mixed $value
     * @return bool|int|float|string|\phpsap\DateTime\SapDateTime|\phpsap\DateTime\SapDateInterval
     */
    public function cast($value)
    {
        static $methods;
        if ($methods === null) {
            $methods = [
                self::TYPE_DATE      => static function ($value) {
                    /**
                     * In case the date value consists only of zeros, this
                     * is most likely a mistake of the SAP remote function.
                     */
                    if (preg_match('~^[0]+$~', $value)) {
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
     * @return \phpsap\classes\Api\Element
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public static function fromArray($array)
    {
        static::fromArrayValidation($array);
        return new self($array[self::JSON_TYPE], $array[self::JSON_NAME]);
    }

    /**
     * Validate the array for fromArray().
     * @param mixed $array
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    protected static function fromArrayValidation($array)
    {
        if (!is_array($array)) {
            throw new InvalidArgumentException(sprintf(
                'Expected array, but got \'%s\'!',
                gettype($array)
            ));
        }
        foreach (static::$allowedKeys as $key) {
            if (!array_key_exists($key, $array)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid JSON: %s is missing %s!',
                    static::class,
                    $key
                ));
            }
        }
    }

    /**
     * Decode a formerly JSON encoded IElement object.
     * @param string $json JSON encoded Element object.
     * @return \phpsap\classes\Api\Element
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public static function jsonDecode($json): \phpsap\interfaces\Util\IJsonSerializable
    {
        $array = static::jsonToArray($json);
        return static::fromArray($array);
    }
}
