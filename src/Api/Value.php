<?php

declare(strict_types=1);

namespace phpsap\classes\Api;

use phpsap\classes\Api\Traits\CastPrimitivesTrait;
use phpsap\classes\Api\Traits\DirectionTrait;
use phpsap\classes\Api\Traits\NameTrait;
use phpsap\classes\Api\Traits\OptionalTrait;
use phpsap\classes\Api\Traits\TypeTrait;
use phpsap\classes\Util\JsonSerializable;
use phpsap\interfaces\Api\IValue;

/**
 * Class Value
 *
 * API values extend the logic of an element but have a direction (input or output)
 * and an optional flag, unlike elements.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
final class Value extends JsonSerializable implements IValue
{
    use TypeTrait;
    use NameTrait;
    use DirectionTrait;
    use OptionalTrait;
    use CastPrimitivesTrait;

    /**
     * @var array<int, string> Allowed JsonSerializable keys to set values for.
     */
    protected static array $allowedKeys = [
        self::JSON_TYPE,
        self::JSON_NAME,
        self::JSON_DIRECTION,
        self::JSON_OPTIONAL
    ];

    /**
     * @inheritDoc
     */
    public function __construct(array $array)
    {
        parent::__construct($array);
        $this->setType($array[self::JSON_TYPE]);
        $this->setName($array[self::JSON_NAME]);
        $this->setDirection($array[self::JSON_DIRECTION]);
        $this->setOptional($array[self::JSON_OPTIONAL]);
    }

    /**
     * @inheritDoc
     */
    public static function create(string $type, string $name, string $direction, bool $isOptional): Value
    {
        return new Value(
            [
                self::JSON_TYPE => $type,
                self::JSON_NAME => $name,
                self::JSON_DIRECTION => $direction,
                self::JSON_OPTIONAL => $isOptional
            ]
        );
    }

    /**
     * @return array<int, string>
     */
    private function getAllowedTypes(): array
    {
        return [
            self::TYPE_BOOLEAN,
            self::TYPE_INTEGER,
            self::TYPE_FLOAT,
            self::TYPE_STRING,
            self::TYPE_HEXBIN,
            self::TYPE_DATE,
            self::TYPE_TIME,
            self::TYPE_TIMESTAMP,
            self::TYPE_WEEK
        ];
    }

    /**
     * @return array<int, string>
     */
    private function getAllowedDirections(): array
    {
        return [
            self::DIRECTION_INPUT,
            self::DIRECTION_OUTPUT
        ];
    }
}
