<?php

declare(strict_types=1);

namespace phpsap\classes\Api;

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
     * @inheritDoc
     */
    private function getAllowedTypes(): array
    {
        return [self::TYPE_STRUCT];
    }

    /**
     * @inheritDoc
     */
    private function getAllowedDirections(): array
    {
        return [
            self::DIRECTION_INPUT,
            self::DIRECTION_OUTPUT
        ];
    }

    /**
     * @inheritDoc
     */
    public function __construct(array $array)
    {
        /** @noinspection DuplicatedCode */
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
                        Struct::class
                    )
                );
            }
            $members[] = new Member($member);
        }
        $this->setMembers($members);
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
}
