<?php

namespace phpsap\classes\Api;

use InvalidArgumentException;
use phpsap\exceptions\ArrayElementMissingException;
use phpsap\interfaces\Api\IArray;
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
class Table extends Value implements IArray
{
    /**
     * @var array List of allowed API element types.
     */
    protected static $allowedTypes = [self::TYPE_ARRAY];

    /**
     * @var array List of allowed API value directions.
     */
    protected static $allowedDirections = [self::DIRECTION_TABLE];

    /**
     * Table constructor.
     * @param string  $name        API struct name.
     * @param bool    $isOptional  Is the API table optional?
     * @param array   $members     Array of Elements as the columns of the table.
     */
    public function __construct($name, $isOptional, $members)
    {
        parent::__construct(self::TYPE_ARRAY, $name, self::DIRECTION_TABLE, $isOptional);
        $this->setMembers($members);
    }

    /**
     * Cast a given value to the implemented value.
     * @param array $table
     * @return array
     */
    public function cast($table)
    {
        foreach ($table as &$row) {
            foreach ($this->getMembers() as $member) {
                /**
                 * @var IElement $member
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
        return $table;
    }

    /**
     * Return an array of member elements.
     * @return array
     */
    public function getMembers()
    {
        return $this->data[self::JSON_MEMBERS];
    }

    /**
     * Add a member to the table.
     * @param \phpsap\interfaces\Api\IElement $member
     * @return \phpsap\classes\Api\Table
     */
    public function addMember(IElement $member)
    {
        $this->data[self::JSON_MEMBERS][] = $member;
        return $this;
    }

    /**
     * Set the member elements of the table.
     * @param array $members
     */
    protected function setMembers($members)
    {
        if (!is_array($members)) {
            throw new InvalidArgumentException(
                'Expected API table members to be in an array!'
            );
        }
        $this->data[self::JSON_MEMBERS] = [];
        foreach ($members as $member) {
            if (!$member instanceof IElement) {
                throw new InvalidArgumentException(
                    'Expected API table members to be instances of IElement!'
                );
            }
            $this->addMember($member);
        }
    }

    /**
     * Decode a formerly JSON encoded Struct object.
     * @param string|\stdClass|array $json
     * @return \phpsap\classes\Api\Table
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
            self::JSON_NAME,
            self::JSON_DIRECTION,
            self::JSON_OPTIONAL,
            self::JSON_MEMBERS
        ];
        foreach ($fields as $field) {
            if (!array_key_exists($field, $json)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid JSON: API Table is missing %s!',
                    $field
                ));
            }
        }
        if ($json[self::JSON_DIRECTION] !== self::DIRECTION_TABLE) {
            throw new InvalidArgumentException('Invalid JSON: API Table direction is not table!');
        }
        if ($json[self::JSON_TYPE] !== self::TYPE_ARRAY) {
            throw new InvalidArgumentException('Invalid JSON: API Table type is not an array!');
        }
        if (!is_array($json[self::JSON_MEMBERS])) {
            throw new InvalidArgumentException('Invalid JSON: API Table members are not an array!');
        }
        $members = [];
        foreach ($json[self::JSON_MEMBERS] as $member) {
            $members[] = Element::jsonDecode($member);
        }
        return new self($json[self::JSON_NAME], $json[self::JSON_OPTIONAL], $members);
    }
}
