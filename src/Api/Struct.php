<?php

namespace phpsap\classes\Api;

use phpsap\exceptions\ArrayElementMissingException;
use phpsap\interfaces\Api\IArray;
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
class Struct extends Value implements IArray
{
    /**
     * @var array List of allowed API element types.
     */
    protected static $allowedTypes = [
        self::TYPE_ARRAY
    ];

    /**
     * Struct constructor.
     * @param string  $name        API struct name.
     * @param string  $direction   Either input or output.
     * @param bool    $isOptional  Is the API struct optional?
     * @param array   $members     Array of Elements as the members of the struct.
     */
    public function __construct($name, $direction, $isOptional, $members)
    {
        parent::__construct(self::TYPE_ARRAY, $name, $direction, $isOptional);
        $this->setMembers($members);
    }

    /**
     * Cast a given value to the implemented value.
     * @param array $struct
     * @return array
     */
    public function cast($struct)
    {
        foreach ($this->getMembers() as $member) {
            /**
             * @var IElement $member
             */
            $name = $member->getName();
            if (!array_key_exists($name, $struct)) {
                throw new ArrayElementMissingException(sprintf(
                    'Element %s in struct %s is missing!',
                    $name,
                    $this->getName()
                ));
            }
            $struct[$name] = $member->cast($struct[$name]);
        }
        return $struct;
    }

    /**
     * Return an array of member elements.
     * @return array
     */
    public function getMembers()
    {
        return $this->data['members'];
    }

    /**
     * Add a member to the table.
     * @param \phpsap\interfaces\Api\IElement $member
     * @return \phpsap\classes\Api\Struct
     */
    public function addMember(IElement $member)
    {
        $this->data['members'][] = $member;
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
                'Expected API struct members to be array!'
            );
        }
        $this->data['members'] = [];
        foreach ($members as $member) {
            $this->addMember($member);
        }
    }
}
