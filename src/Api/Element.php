<?php

namespace phpsap\classes\Api;

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
        self::TYPE_STRING
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
        return $this->data['type'];
    }

    /**
     * The name of the element.
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * Set optional the API element PHP type.
     * @param string $type
     */
    protected function setType($type)
    {
        if (!is_string($type)) {
            throw new \InvalidArgumentException(
                'Expected API element type to be string!'
            );
        }
        if (!in_array($type, static::$allowedTypes, true)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected API element type to be in: %s!',
                implode(', ', static::$allowedTypes)
            ));
        }
        $this->data['type'] = $type;
    }

    /**
     * Set the API element name.
     * @param string $name
     */
    protected function setName($name)
    {
        if (!is_string($name) || $name === '') {
            throw new \InvalidArgumentException(
                'Expected API element name to be string!'
            );
        }
        $this->data['name'] = $name;
    }

    /**
     * Cast a given value to the type defined in this class.
     * @param mixed $value
     * @return bool|int|float|string
     */
    public function cast($value)
    {
        settype($value, $this->getType());
        return $value;
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
}
