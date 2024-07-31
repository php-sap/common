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
use phpsap\interfaces\Api\ITable;

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
final class Table extends JsonSerializable implements ITable
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
        return [self::TYPE_TABLE];
    }

    /**
     * @return array<int, string>
     */
    protected function getAllowedDirections(): array
    {
        return [
            self::DIRECTION_INPUT,
            self::DIRECTION_OUTPUT,
            self::DIRECTION_TABLE
        ];
    }

    /**
     * @inheritDoc
     */
    public static function create(string $name, string $direction, bool $isOptional, array $members): Table
    {
        $table = new Table(
            [
                self::JSON_TYPE => self::TYPE_TABLE, //it's always 'table'
                self::JSON_NAME => $name,
                self::JSON_DIRECTION => $direction,
                self::JSON_OPTIONAL => $isOptional,
                self::JSON_MEMBERS => []
            ]
        );
        $table->setMembers($members);
        return $table;
    }

    /**
     * Cast a given value to the implemented value.
     * @param array<int, array<string, null|bool|int|float|string>> $value
     * @return array<int, array<string, null|bool|int|float|string|DateTime|DateInterval>>
     * @throws ArrayElementMissingException
     * @throws InvalidArgumentException
     */
    public function cast(array $value): array
    {
        foreach ($value as $index => $row) {
            foreach ($this->getMembers() as $member) {
                $name = $member->getName();
                if (!array_key_exists($name, $row)) {
                    throw new ArrayElementMissingException(sprintf(
                        'Element %s in table %s line %u is missing!',
                        $name,
                        $this->getName(),
                        $index
                    ));
                }
                $value[$index][$name] = $member->cast($row[$name]);
            }
        }
        return $value;
    }
}
