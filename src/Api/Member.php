<?php

declare(strict_types=1);

namespace phpsap\classes\Api;

use phpsap\classes\Api\Traits\CastPrimitivesTrait;
use phpsap\classes\Api\Traits\NameTrait;
use phpsap\classes\Api\Traits\TypeTrait;
use phpsap\classes\Util\JsonSerializable;
use phpsap\interfaces\Api\IMember;

/**
 * Class Member
 */
final class Member extends JsonSerializable implements IMember
{
    use TypeTrait;
    use NameTrait;
    use CastPrimitivesTrait;

    /**
     * @inheritDoc
     */
    public function __construct(array $array)
    {
        parent::__construct($array);
        $this->setType($array[self::JSON_TYPE]);
        $this->setName($array[self::JSON_NAME]);
    }

    /**
     * @inheritDoc
     */
    public static function create(string $type, string $name): Member
    {
        return new Member(
            [
                self::JSON_TYPE => $type,
                self::JSON_NAME => $name
            ]
        );
    }

    /**
     * Get an array of all valid keys this class is able to set().
     * @return array<int, string>
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getAllowedKeys(): array
    {
        return [
            self::JSON_TYPE,
            self::JSON_NAME
        ];
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
}