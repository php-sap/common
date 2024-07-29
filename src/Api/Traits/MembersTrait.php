<?php

declare(strict_types=1);

namespace phpsap\classes\Api\Traits;

use phpsap\classes\Api\Member;
use phpsap\exceptions\InvalidArgumentException;

/**
 * Trait MembersTrait
 */
trait MembersTrait
{
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
     * @param array<int, Member> $members
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
}
