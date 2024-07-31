<?php

declare(strict_types=1);

namespace phpsap\classes\Api;

use JsonException;
use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IApiElement;
use phpsap\interfaces\Api\IStruct;
use phpsap\interfaces\Api\ITable;
use phpsap\interfaces\Api\IApi;
use phpsap\interfaces\Util\IJsonSerializable;

/**
 * Class RemoteApi
 *
 * This class contains the description of a single SAP remote function API.
 *
 * @package phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
final class RemoteApi implements IApi
{
    /**
     * @var IApiElement[]
     */
    private array $elements = [];

    /**
     * Add an input value of the remote function.
     * @param IApiElement $element
     * @return RemoteApi
     */
    public function add(IApiElement $element): RemoteApi
    {
        $this->elements[] = $element;
        return $this;
    }

    /**
     * Get all input values of the remote function.
     * @return IApiElement[]
     */
    public function getInputElements(): array
    {
        return $this->getElements(IApiElement::DIRECTION_INPUT);
    }

    /**
     * Get all output values of the remote function.
     * @return IApiElement[]
     */
    public function getOutputElements(): array
    {
        return $this->getElements(IApiElement::DIRECTION_OUTPUT);
    }

    /**
     * Get all tables of the remote function.
     * @return IApiElement[]
     */
    public function getTables(): array
    {
        return $this->getElements(ITable::DIRECTION_TABLE);
    }

    /**
     * Get the API values according to their direction: input, output, table.
     * @param string $direction
     * @return IApiElement[]
     */
    private function getElements(string $direction): array
    {
        $result = [];
        foreach ($this->elements as $element) {
            if ($element->getDirection() === $direction) {
                $result[] = $element;
            }
        }
        return $result;
    }

    /**
     * Create a remote API from a given array.
     * @param array<int, array<string, string|bool|array<int, array<string, string>>>> $array Array of remote API elements.
     * @throws InvalidArgumentException
     */
    public function __construct(array $array = [])
    {
        foreach ($array as $item) {
            $this->elements[] = $this->buildElement($item);
        }
    }

    /**
     * Call type-specific constructors for the given array.
     * @param array<string, string|bool|array<int, array<string, string>>> $array
     * @return IApiElement
     * @throws InvalidArgumentException
     */
    private function buildElement(array $array): IApiElement
    {
        if (!array_key_exists(IApiElement::JSON_TYPE, $array)) {
            throw new InvalidArgumentException('API element is missing type.');
        }
        if ($array[IApiElement::JSON_TYPE] === ITable::TYPE_TABLE) {
            return new Table($array);
        }
        if ($array[IApiElement::JSON_TYPE] === IStruct::TYPE_STRUCT) {
            return new Struct($array);
        }
        return new Value($array);
    }

    /**
     * Decode a formerly JSON encoded IApi object.
     * @param string $json JSON encoded remote API object.
     * @return RemoteApi
     * @throws InvalidArgumentException
     */
    public static function jsonDecode(string $json): IJsonSerializable
    {
        try {
            $array = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($array)) {
                throw new InvalidArgumentException('JSON did not decode into an array!');
            }
            return new self($array);
        } catch (InvalidArgumentException | JsonException $exception) {
            throw new InvalidArgumentException(
                sprintf('Invalid JSON: Expected JSON encoded %s string!', self::class),
                0,
                $exception
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->elements;
    }
}
