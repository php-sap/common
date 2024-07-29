<?php

declare(strict_types=1);

namespace phpsap\classes\Api\Traits;

use phpsap\classes\Api\Member;
use phpsap\classes\Api\Struct;
use phpsap\classes\Api\Table;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IMember;

/**
 * Trait MembersTrait
 */
trait MembersTrait
{
    /**
     * @var array<int, string> Allowed JsonSerializable keys to set values for.
     */
    protected static array $allowedKeys = [
        self::JSON_TYPE,
        self::JSON_NAME,
        self::JSON_DIRECTION,
        self::JSON_OPTIONAL,
        self::JSON_MEMBERS
    ];

    /**
     * Return an array of member elements.
     * @return IMember[]
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
     * @param array<int, IMember> $members
     * @throws InvalidArgumentException
     */
    protected function setMembers(array $members): void
    {
        foreach ($members as $member) {
            if (!$member instanceof Member) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Expected API %s members to be instances of %s!',
                        static::class,
                        Member::class
                    )
                );
            }
        }
        $this->remove(self::JSON_MEMBERS);
        $this->set(self::JSON_MEMBERS, $members);
    }

    /**
     * @inheritDoc
     */
    public function __construct(array $array)
    {
        /** @noinspection PhpMultipleClassDeclarationsInspection */
        parent::__construct($array);
        $this->setType($array[self::JSON_TYPE]);
        $this->setName($array[self::JSON_NAME]);
        $this->setDirection($array[self::JSON_DIRECTION]);
        $this->setOptional($array[self::JSON_OPTIONAL]);
        $members = [];
        foreach ($array[self::JSON_MEMBERS] as $member) {
            if (!is_array($member)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid JSON: API %s members are not an array!',
                        self::class
                    )
                );
            }
            $members[] = new Member($member);
        }
        $this->setMembers($members);
    }
}
