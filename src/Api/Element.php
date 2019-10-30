<?php

namespace phpsap\classes\Api;

use InvalidArgumentException;
use phpsap\DateTime\SapDateTime;
use phpsap\interfaces\Api\IElement;

/**
 * Class phpsap\classes\Api\Element
 *
 * API elements are struct or table values and have no direction or optional flag of
 * their own.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class Element implements IElement
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array List of allowed API element types.
     */
    protected static $allowedTypes = [
        self::TYPE_BOOLEAN,
        self::TYPE_INTEGER,
        self::TYPE_FLOAT,
        self::TYPE_STRING,
        self::TYPE_HEX2BIN,
        self::TYPE_DATE,
        self::TYPE_TIME,
        self::TYPE_TIMESTAMP,
        self::TYPE_WEEK
    ];

    /**
     * API element constructor.
     * @param string $type Either string, int, float, bool or array
     * @param string $name API element name.
     */
    public function __construct($type, $name)
    {
        $this->setType($type);
        $this->setName($name);
    }

    /**
     * The PHP type of the element.
     * @return string
     */
    public function getType()
    {
        return $this->data[self::JSON_TYPE];
    }

    /**
     * The name of the element.
     * @return string
     */
    public function getName()
    {
        return $this->data[self::JSON_NAME];
    }

    /**
     * Set optional the API element PHP type.
     * @param string $type
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
        $this->data[self::JSON_TYPE] = $type;
    }

    /**
     * Set the API element name.
     * @param string $name
     */
    protected function setName($name)
    {
        if (!is_string($name) || $name === '') {
            throw new InvalidArgumentException(
                'Expected API element name to be string!'
            );
        }
        $this->data[self::JSON_NAME] = $name;
    }

    /**
     * Cast a given output value to the type defined in this class.
     * @param mixed $value
     * @return bool|int|float|string|\phpsap\DateTime\SapDateTime
     * @throws \Exception
     */
    public function cast($value)
    {
        $type = $this->getType();
        switch ($type) {
            case self::TYPE_DATE:
                $result = SapDateTime::createFromFormat(SapDateTime::SAP_DATE, $value);
                break;
            case self::TYPE_TIME:
                $result = SapDateTime::createFromFormat(SapDateTime::SAP_TIME, $value);
                break;
            case self::TYPE_TIMESTAMP:
                $result = SapDateTime::createFromFormat(SapDateTime::SAP_TIMESTAMP, $value);
                break;
            case self::TYPE_WEEK:
                $result = SapDateTime::createFromFormat(SapDateTime::SAP_WEEK, $value);
                break;
            case self::TYPE_HEX2BIN:
                $result = hex2bin($value);
                break;
            default:
                $result = $value;
                settype($result, $type);
                break;
        }
        return $result;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by json_encode,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * Decode a formerly JSON encoded Element object.
     * @param string|\stdClass|array $json
     * @return \phpsap\classes\Api\Element
     */
    public static function jsonDecode($json)
    {
        if (is_object($json)) {
            $json = json_encode($json);
        }
        if (is_string($json)) {
            $json = json_decode($json, true);
        }
        if (!is_array($json)) {
            throw new InvalidArgumentException('Invalid JSON!');
        }
        $fields = [
            self::JSON_TYPE,
            self::JSON_NAME
        ];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $json)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid JSON: API Element is missing %s!',
                    $field
                ));
            }
        }
        return new self($json[self::JSON_TYPE], $json[self::JSON_NAME]);
    }
}
