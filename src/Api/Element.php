<?php

declare(strict_types=1);

namespace phpsap\classes\Api;

use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IElement;

/**
 * Class Element
 *
 * API elements are struct or table values and have no direction or optional flag of
 * their own.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class Element extends JsonSerializable implements IElement
{
    /**
     * @var array Allowed JsonSerializable keys to set values for.
     */
    protected static array $allowedKeys = [
        self::JSON_TYPE,
        self::JSON_NAME,
        self::JSON_DIRECTION,
        self::JSON_OPTIONAL
    ];

    /**
     * @var array List of allowed API element types.
     */
    protected static array $allowedTypes = [];

    /**
     * @var array List of allowed API value directions.
     */
    protected static array $allowedDirections = [
        self::DIRECTION_INPUT,
        self::DIRECTION_OUTPUT
    ];

    /**
     * API element constructor.
     * @param string $type Either string, int, float, bool or array
     * @param string $name API element name.
     * @param string $direction Either input, output or table.
     * @param bool $isOptional Is the API value optional?
     * @throws InvalidArgumentException
     */
    public function __construct(string $type, string $name, string $direction, bool $isOptional)
    {
        parent::__construct();
        $this->setType($type);
        $this->setName($name);
        $this->setDirection($direction);
        $this->setOptional($isOptional);
    }

    /**
     * The PHP type of the element.
     * @return string
     * @throws InvalidArgumentException
     */
    public function getType(): string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_TYPE);
    }

    /**
     * Set optional the API element PHP type.
     * @param string $type
     * @throws InvalidArgumentException
     */
    protected function setType(string $type): void
    {
        if (!in_array($type, static::$allowedTypes, true)) {
            throw new InvalidArgumentException(sprintf(
                'Expected API element type to be in: %s!',
                implode(', ', static::$allowedTypes)
            ));
        }
        $this->set(self::JSON_TYPE, $type);
    }

    /**
     * The name of the element.
     * @return string
     * @throws InvalidArgumentException
     */
    public function getName(): string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_NAME);
    }

    /**
     * Set the API element name.
     * @param string $name
     * @throws InvalidArgumentException
     */
    protected function setName(string $name): void
    {
        if (trim($name) === '') {
            throw new InvalidArgumentException(
                'Expected API element name to be string!'
            );
        }
        $this->set(self::JSON_NAME, trim($name));
    }

    /**
     * Get the direction of the API value.
     * @return string
     * @throws InvalidArgumentException
     */
    public function getDirection(): string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_DIRECTION);
    }

    /**
     * Set the API value direction: input, output or table.
     * @param string $direction
     * @throws InvalidArgumentException
     */
    protected function setDirection(string $direction): void
    {
        if (!in_array($direction, static::$allowedDirections, true)) {
            throw new InvalidArgumentException(sprintf(
                'Expected API value direction to be in: %s!',
                implode(', ', static::$allowedDirections)
            ));
        }
        $this->set(self::JSON_DIRECTION, $direction);
    }

    /**
     * Is the value optional?
     * @return bool
     * @throws InvalidArgumentException
     */
    public function isOptional(): bool
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_OPTIONAL);
    }

    /**
     * Set the API value optional flag.
     * @param bool $isOptional
     * @throws InvalidArgumentException
     */
    protected function setOptional(bool $isOptional): void
    {
        $this->set(self::JSON_OPTIONAL, $isOptional);
    }

    /**
     * Create an instance of this class from an array.
     * @param array $array Array containing the properties of this class.
     * @return Element
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $array): Element
    {
        static::fromArrayValidation($array);
        return new self(
            $array[self::JSON_TYPE],
            $array[self::JSON_NAME],
            $array[self::JSON_DIRECTION],
            $array[self::JSON_OPTIONAL]
        );
    }

    /**
     * Validate the array for fromArray().
     * @param array $array
     * @return void
     * @throws InvalidArgumentException
     */
    protected static function fromArrayValidation(array $array): void
    {
        foreach (static::$allowedKeys as $key) {
            if (!array_key_exists($key, $array)) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid JSON: %s is missing %s!',
                    static::class,
                    $key
                ));
            }
        }
    }

    /**
     * Decode a formerly JSON encoded IElement object.
     * @param string $json JSON encoded Element object.
     * @return Element
     * @throws InvalidArgumentException
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function jsonDecode(string $json): IElement
    {
        $array = static::jsonToArray($json);
        return static::fromArray($array);
    }
}
