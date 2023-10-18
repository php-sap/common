<?php

namespace phpsap\classes\Api;

use phpsap\exceptions\InvalidArgumentException;
use phpsap\exceptions\ArrayElementMissingException;
use phpsap\interfaces\Api\IStruct;
use phpsap\interfaces\Api\IElement;

/**
 * Class phpsap\classes\Api\Struct
 *
 * API structs behave like values but contain elements as members.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class Struct extends Value implements IStruct
{
    /**
     * @var array Allowed JsonSerializable keys to set values for.
     */
    protected static array $allowedKeys = [
        self::JSON_TYPE,
        self::JSON_NAME,
        self::JSON_DIRECTION,
        self::JSON_OPTIONAL,
        self::JSON_MEMBERS
    ];

    /**
     * @var array List of allowed API element types.
     */
    protected static array $allowedTypes = [
        self::TYPE_STRUCT
    ];

    /**
     * Get an array of all valid configuration keys and whether they are mandatory.
     * @return array
     */
    protected function getAllowedKeys(): array
    {
        //Merge the keys of Element, Value and Struct class.
        return array_merge(parent::getAllowedKeys(), self::$allowedKeys);
    }

    /**
     * Struct constructor.
     * @param string $name       API struct name.
     * @param string $direction  Either input or output.
     * @param bool   $isOptional Is the API struct optional?
     * @param array  $members    Array of Elements as the members of the struct.
     * @throws InvalidArgumentException
     */
    public function __construct($name, $direction, $isOptional, $members)
    {
        parent::__construct(self::TYPE_STRUCT, $name, $direction, $isOptional);
        $this->setMembers($members);
    }

    /**
     * Cast a given value to the implemented value.
     * @param array $value The output array to typecast.
     * @return array
     * @throws ArrayElementMissingException
     */
    public function cast($value): array
    {
        foreach ($this->getMembers() as $member) {
            /**
             * @var Element $member
             */
            $name = $member->getName();
            if (!array_key_exists($name, $value)) {
                throw new ArrayElementMissingException(sprintf(
                    'Element %s in struct %s is missing!',
                    $name,
                    $this->getName()
                ));
            }
            $value[$name] = $member->cast($value[$name]);
        }
        return $value;
    }

    /**
     * Return an array of member elements.
     * @return array
     */
    public function getMembers(): array
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_MEMBERS);
    }

    /**
     * Set the member elements of the table.
     * @param array $members
     * @throws InvalidArgumentException
     */
    protected function setMembers($members)
    {
        if (!is_array($members)) {
            throw new InvalidArgumentException(
                'Expected API struct members to be in an array!'
            );
        }
        foreach ($members as $member) {
            if (!$member instanceof IElement) {
                throw new InvalidArgumentException(
                    'Expected API struct members to be instances of IElement!'
                );
            }
        }
        $this->remove(self::JSON_MEMBERS);
        $this->set(self::JSON_MEMBERS, $members);
    }

    /**
     * Create an instance of this class from an array.
     * @param array $array Array containing the properties of this class.
     * @return Struct
     * @throws InvalidArgumentException
     */
    public static function fromArray($array): Struct
    {
        static::fromArrayValidation($array);
        if ($array[self::JSON_TYPE] !== self::TYPE_STRUCT) {
            throw new InvalidArgumentException('Invalid JSON: API Struct type is not an array!');
        }
        if (!is_array($array[self::JSON_MEMBERS])) {
            throw new InvalidArgumentException('Invalid JSON: API Struct members are not an array!');
        }
        $members = [];
        foreach ($array[self::JSON_MEMBERS] as $member) {
            $members[] = Element::fromArray($member);
        }
        return new self(
            $array[self::JSON_NAME],
            $array[self::JSON_DIRECTION],
            $array[self::JSON_OPTIONAL],
            $members
        );
    }
}
