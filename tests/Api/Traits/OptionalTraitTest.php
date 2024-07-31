<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Api\Traits;

use phpsap\classes\Api\Value;
use phpsap\interfaces\Api\IApiElement;
use phpsap\interfaces\Api\IValue;
use phpsap\interfaces\exceptions\IInvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Class OptionalTraitTest
 */
class OptionalTraitTest extends TestCase
{
    /**
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws IInvalidArgumentException
     */
    public function testOptionalFalse(): void
    {
        $value = Value::create(IValue::TYPE_INTEGER, 'diw6vi7d', IApiElement::DIRECTION_INPUT, false);
        static::assertFalse($value->isOptional());
    }

    /**
     * @return void
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws IInvalidArgumentException
     */
    public function testOptionalTrue(): void
    {
        $value = Value::create(IValue::TYPE_BOOLEAN, 'ima4wmam', IApiElement::DIRECTION_OUTPUT, true);
        static::assertTrue($value->isOptional());
    }
}
