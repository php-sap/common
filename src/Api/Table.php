<?php

namespace phpsap\classes\Api;

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
        return $this->data['columns'];
    }

    /**
     * Add a member to the table.
     * @param \phpsap\interfaces\Api\IElement $member
     * @return \phpsap\classes\Api\Table
     */
    public function addMember(IElement $member)
    {
        $this->data['columns'][] = $member;
        return $this;
    }

    /**
     * Set the member elements of the table.
     * @param array $members
     */
    protected function setMembers($members)
    {
        if (!is_array($members)) {
            throw new \InvalidArgumentException(
                'Expected API table members to be array!'
            );
        }
        $this->data['columns'] = [];
        foreach ($members as $member) {
            $this->addMember($member);
        }
    }
}
