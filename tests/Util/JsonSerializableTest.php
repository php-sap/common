<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Util;

use phpsap\exceptions\InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use phpsap\interfaces\Util\IJsonSerializable;
use phpsap\classes\Util\JsonSerializable;
use tests\phpsap\classes\helper\PublicJsonSerializable;

/**
 * Class JsonSerializableTest
 *
 * Test the JsonSerializable class.
 *
 * @package tests\phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
class JsonSerializableTest extends TestCase
{
    /**
     * Test the class inheritance chain.
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testInheritance(): void
    {
        $obj = new PublicJsonSerializable();
        static::assertInstanceOf(\JsonSerializable::class, $obj);
        static::assertInstanceOf(JsonSerializable::class, $obj);
        static::assertSame([], $obj->toArray());
    }

    /**
     * Test the successful storage of data.
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     */
    public function testSuccessfulDataStorage(): void
    {
        PublicJsonSerializable::$allowedKeys = [
            'bhtWTHMh',
            'GcnIscoB',
            'FsU1looN',
            'jlxFb5gL',
            'ktOFBe9N',
            'variable'
        ];
        $store = new PublicJsonSerializable(['variable' => '']);
        $store->set('bhtWTHMh', 54247);
        $store->set('GcnIscoB', 95.31);
        $store->set('FsU1looN', true);
        $store->set('jlxFb5gL', false);
        $store->set('ktOFBe9N', ['KTEfbUvj' => 294]);
        $store->set('variable', 'bfbp8A0VIo');
        static::assertTrue($store->has('variable'));
        static::assertTrue($store->has('bhtWTHMh'));
        static::assertTrue($store->has('GcnIscoB'));
        static::assertTrue($store->has('FsU1looN'));
        static::assertTrue($store->has('jlxFb5gL'));
        static::assertTrue($store->has('ktOFBe9N'));
        static::assertSame('bfbp8A0VIo', $store->get('variable'));
        static::assertSame(54247, $store->get('bhtWTHMh'));
        static::assertSame(95.31, $store->get('GcnIscoB'));
        static::assertTrue($store->get('FsU1looN'));
        static::assertFalse($store->get('jlxFb5gL'));
        static::assertSame(['KTEfbUvj' => 294], $store->get('ktOFBe9N'));
        $store->set('variable', null);
        $store->remove('bhtWTHMh');
        static::assertFalse($store->has('variable'));
        static::assertFalse($store->has('bhtWTHMh'));
        static::assertNull($store->get('variable'));
        static::assertNull($store->get('bhtWTHMh'));
        $actual_json = json_encode($store);
        $expected_json = '{"GcnIscoB":95.31,"FsU1looN":true,"jlxFb5gL":false,"ktOFBe9N":{"KTEfbUvj":294}}';
        static::assertSame($expected_json, $actual_json);
        $store->reset();
        static::assertSame([], $store->toArray());
    }

    /**
     * Data provider for invalid keys.
     * @return array<int, array<int, string>>
     */
    public static function provideInvalidKeys(): array
    {
        return [
            [''],
            [' '],
            ['dY1 Us-xO_L5H'],
            ['phoj.hbd.tcf'],
        ];
    }

    /**
     * Test invalid keys.
     * @param string $key
     * @dataProvider provideInvalidKeys
     */
    public function testInvalidKeys(string $key): void
    {
        $store = new PublicJsonSerializable();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid key!');
        $store->has($key);
    }

    /**
     * Test setting an unknown key.
     */
    public function testSettingUnknownKey(): void
    {
        PublicJsonSerializable::$allowedKeys = [];
        $store = new PublicJsonSerializable();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown key \'OdUoAAih\'!');
        $store->set('OdUoAAih', 4.867);
    }

    /**
     * Test decoding a JSON encoded object.
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testDecodingObjectFromJson(): void
    {
        PublicJsonSerializable::$allowedKeys = ['variable'];
        $json = '{"variable":"eG8B39RG"}';
        $obj = PublicJsonSerializable::jsonDecode($json);
        static::assertInstanceOf(IJsonSerializable::class, $obj);
        static::assertInstanceOf(JsonSerializable::class, $obj);
        static::assertInstanceOf(PublicJsonSerializable::class, $obj);
        static::assertTrue($obj->has('variable'));
        static::assertSame('eG8B39RG', $obj->get('variable'));
    }
}
