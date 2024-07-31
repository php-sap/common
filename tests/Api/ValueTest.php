<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Api;

use phpsap\exceptions\InvalidArgumentException;
use phpsap\interfaces\Api\IApiElement;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;
use phpsap\interfaces\Api\IValue;
use phpsap\classes\Api\Value;

/**
 * Class tests\phpsap\classes\Api\ValueTest
 * @package tests\phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class ValueTest extends TestCase
{
    /**
     * Test JSON decode.
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testJsonDecode(): void
    {
        $element = Value::jsonDecode('{"type":"int","name":"JCmy98c0","direction":"input","optional":false}');
        static::assertInstanceOf(Value::class, $element);
        static::assertSame(IValue::TYPE_INTEGER, $element->getType());
        static::assertSame('JCmy98c0', $element->getName());
        static::assertSame(IApiElement::DIRECTION_INPUT, $element->getDirection());
        static::assertFalse($element->isOptional());
    }

    /**
     * Test JSON decoding on invalid parameters.
     * @param string $json
     * @dataProvider \tests\phpsap\classes\Api\MemberTest::provideInvalidJsonString()
     */
    public function testInvalidJsonString(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON: Expected JSON encoded');
        Value::jsonDecode($json);
    }

    /**
     * Data provider for incomplete JSON objects.
     * @return array<int, array<int, string>>
     */
    public static function provideIncompleteJsonObjects(): array
    {
        $return = MemberTest::provideIncompleteJsonObjects();
        $return[] = ['{"type":"int","name":"TRD2cpKy"}'];
        $return[] = ['{"type":true,"name":"H5vNFNkl","optional":true}'];
        $return[] = ['{"type":"int","name":711,"direction":"output"}'];
        return $return;
    }

    /**
     * Test JSON decoding on incomplete JSON objects.
     * @param string $json
     * @dataProvider provideIncompleteJsonObjects
     */
    public function testIncompleteJson(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON:');
        Value::jsonDecode($json);
    }
}
