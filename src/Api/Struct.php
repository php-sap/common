<?php

declare(strict_types=1);

namespace phpsap\classes\Api;

use DateInterval;
use DateTime;
use phpsap\classes\Api\Traits\ConstructorTrait;
use phpsap\classes\Api\Traits\DirectionTrait;
use phpsap\classes\Api\Traits\MembersTrait;
use phpsap\classes\Api\Traits\NameTrait;
use phpsap\classes\Api\Traits\OptionalTrait;
use phpsap\classes\Api\Traits\TypeTrait;
use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\exceptions\ArrayElementMissingException;
use phpsap\interfaces\Api\IStruct;

/**
 * Class phpsap\classes\Api\Struct
 *
 * API structs behave like values but contain elements as members.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
final class Struct extends JsonSerializable implements IStruct
{
    use TypeTrait;
    use NameTrait;
    use DirectionTrait;
    use OptionalTrait;
    use MembersTrait;
    use ConstructorTrait;

    /**
     * Get an array of all valid keys this class is able to set().
     * @return array<int, string>
     */
    protected function getAllowedKeys(): array
    {
        return [
            self::JSON_TYPE,
            self::JSON_NAME,
            self::JSON_DIRECTION,
            self::JSON_OPTIONAL,
            self::JSON_MEMBERS,
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function getAllowedTypes(): array
    {
        return [self::TYPE_STRUCT];
    }

    /**
     * @return array<int, string>
     */
    protected function getAllowedDirections(): array
    {
        return [
            self::DIRECTION_INPUT,
            self::DIRECTION_OUTPUT,
            self::DIRECTION_CHANGING
        ];
    }

    /**
     * @inheritDoc
     */
    public static function create(string $name, string $direction, bool $isOptional, array $members): Struct
    {
        $struct = new Struct(
            [
                self::JSON_TYPE => self::TYPE_STRUCT, //it's always 'struct'
                self::JSON_NAME => $name,
                self::JSON_DIRECTION => $direction,
                self::JSON_OPTIONAL => $isOptional,
                self::JSON_MEMBERS => []
            ]
        );
        $struct->setMembers($members);
        return $struct;
    }

    /**
     * Cast a given value to the implemented value.
     * @param array<string, null|bool|int|float|string> $value The output array to typecast.
     * @return array<string, null|bool|int|float|string|DateTime|DateInterval>
     * @throws ArrayElementMissingException
     * @throws InvalidArgumentException
     */
    public function cast(array $value): array
    {
        $result = [];
        foreach ($this->getMembers() as $member) {
            $name = $member->getName();
            if (!array_key_exists($name, $value)) {
                throw new ArrayElementMissingException(sprintf(
                    'Element %s in struct %s is missing!',
                    $name,
                    $this->getName()
                ));
            }
            $result[$name] = $member->cast($value[$name]);
        }
        /**
         * in case there are more values than members, merge these
         * superfluous values into the result.
         */
        return array_merge($value, $result);
    }
}
