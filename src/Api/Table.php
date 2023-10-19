<?php

namespace phpsap\classes\Api;

use phpsap\exceptions\InvalidArgumentException;
use phpsap\exceptions\ArrayElementMissingException;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\Api\IElement;

/**
 * Class phpsap\classes\Api\Table
 *
 * API tables behave like values but contain rows with columns (elements) as members.
 * API tables have no direction!
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class Table extends Value implements ITable
{
    /**
     * @var array Allowed JsonSerializable keys to set values for.
     */
    protected static array $allowedKeys = [
        self::JSON_TYPE,
        self::JSON_NAME,
        self::JSON_DIRECTION,
        self::JSON_OPTIONAL,
        self::JSON_OPTIONAL,
        self::JSON_MEMBERS
    ];

    /**
     * @var array List of allowed API element types.
     */
    protected static array $allowedTypes = [self::TYPE_TABLE];

    /**
     * @var array List of allowed API value directions.
     */
    protected static array $allowedDirections = [
        self::DIRECTION_INPUT,
        self::DIRECTION_OUTPUT,
        self::DIRECTION_TABLE
    ];

    /**
     * Table constructor.
     * @param string $name       API struct name.
     * @param string $direction  Either input, output, or table
     * @param bool   $isOptional Is the API table optional?
     * @param array  $members    Array of Elements as the columns of the table.
     * @throws InvalidArgumentException
     */
    public function __construct(string $name, string $direction, bool $isOptional, array $members)
    {
        parent::__construct(self::TYPE_TABLE, $name, $direction, $isOptional);
        $this->setMembers($members);
    }

    /**
     * Cast a given value to the implemented value.
     * @param array $value
     * @return array
     * @throws ArrayElementMissingException
     * @throws InvalidArgumentException
     */
    public function cast($value): array
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected table cast to be array, %s given!',
                    gettype($value)
                )
            );
        }
        foreach ($value as &$row) {
            foreach ($this->getMembers() as $member) {
                /**
                 * @var Element $member
                 */
                $name = $member->getName();
                if (!array_key_exists($name, $row)) {
                    throw new ArrayElementMissingException(sprintf(
                        'Element %s in table %s is missing!',
                        $name,
                        $this->getName()
                    ));
                }
                $row[$name] = $member->cast($row[$name]);
            }
        }
        unset($row);
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
     * Set the member elements of the table.
     * @param array $members
     * @throws InvalidArgumentException
     */
    protected function setMembers(array $members)
    {
        foreach ($members as $member) {
            if (!$member instanceof IElement) {
                throw new InvalidArgumentException(
                    'Expected API table members to be instances of IElement!'
                );
            }
        }
        $this->remove(self::JSON_MEMBERS);
        $this->set(self::JSON_MEMBERS, $members);
    }

    /**
     * Create an instance of this class from an array.
     * @param array $array Array containing the properties of this class.
     * @return Table
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $array): Table
    {
        static::fromArrayValidation($array);
        if ($array[self::JSON_DIRECTION] !== self::DIRECTION_TABLE) {
            throw new InvalidArgumentException('Invalid JSON: API Table direction is not table!');
        }
        if ($array[self::JSON_TYPE] !== self::TYPE_TABLE) {
            throw new InvalidArgumentException('Invalid JSON: API Table type is not an array!');
        }
        if (!is_array($array[self::JSON_MEMBERS])) {
            throw new InvalidArgumentException('Invalid JSON: API Table members are not an array!');
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
