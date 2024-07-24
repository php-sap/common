<?php

declare(strict_types=1);

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
class Struct extends Element implements IStruct
{
    /**
     * @var array Allowed JsonSerializable keys to set values for.
     */
    protected static array $allowedKeys = [
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
    public function __construct(string $name, string $direction, bool $isOptional, array $members)
    {
        parent::__construct(self::TYPE_STRUCT, $name, $direction, $isOptional);
        $this->setMembers($members);
    }

    /**
     * Cast a given value to the implemented value.
     * @param array $value The output array to typecast.
     * @return array
     * @throws ArrayElementMissingException
     * @throws InvalidArgumentException
     */
    public function cast(array $value): array
    {
        foreach ($this->getMembers() as $member) {
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
     * @throws InvalidArgumentException
     */
    public function getMembers(): array
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_MEMBERS);
    }

    /**
     * Set the member elements of the struct.
     * @param array $members
     * @throws InvalidArgumentException
     */
    protected function setMembers(array $members): void
    {
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
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function fromArray(array $array): Struct
    {
        static::fromArrayValidation($array);
        if ($array[self::JSON_TYPE] !== self::TYPE_STRUCT) {
            throw new InvalidArgumentException('Invalid JSON: API type is not a struct!');
        }
        if (!is_array($array[self::JSON_MEMBERS])) {
            throw new InvalidArgumentException('Invalid JSON: API Struct members are not an array!');
        }
        $members = [];
        foreach ($array[self::JSON_MEMBERS] as $member) {
            /**
             * struct members are values
             */
            $members[] = Value::fromArray($member);
        }
        return new self(
            $array[self::JSON_NAME],
            $array[self::JSON_DIRECTION],
            $array[self::JSON_OPTIONAL],
            $members
        );
    }
}
