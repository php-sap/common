<?php

declare(strict_types=1);

namespace tests\phpsap\classes\Util;

use JsonException;
use phpsap\exceptions\InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;
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
     * @throws JsonException
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
            'fVWlA9ea'
        ];
        $store = new PublicJsonSerializable(
            [
                'bhtWTHMh' => 54247,
                'GcnIscoB' => 95.31,
                'FsU1looN' => true,
                'jlxFb5gL' => false,
                'ktOFBe9N' => ['KTEfbUvj' => 294],
                'fVWlA9ea' => ''
            ]
        );
        $store->set('fVWlA9ea', 'bfbp8A0VIo');
        static::assertTrue($store->has('fVWlA9ea'));
        static::assertTrue($store->has('bhtWTHMh'));
        static::assertTrue($store->has('GcnIscoB'));
        static::assertTrue($store->has('FsU1looN'));
        static::assertTrue($store->has('jlxFb5gL'));
        static::assertTrue($store->has('ktOFBe9N'));
        static::assertSame('bfbp8A0VIo', $store->get('fVWlA9ea'));
        static::assertSame(54247, $store->get('bhtWTHMh'));
        static::assertSame(95.31, $store->get('GcnIscoB'));
        static::assertTrue($store->get('FsU1looN'));
        static::assertFalse($store->get('jlxFb5gL'));
        static::assertSame(['KTEfbUvj' => 294], $store->get('ktOFBe9N'));
        $store->set('fVWlA9ea', null);
        $store->remove('bhtWTHMh');
        static::assertFalse($store->has('fVWlA9ea'));
        static::assertFalse($store->has('bhtWTHMh'));
        static::assertNull($store->get('fVWlA9ea'));
        static::assertNull($store->get('bhtWTHMh'));
        $actual_json = json_encode($store);
        $expected_json = '{"GcnIscoB":95.31,"FsU1looN":true,"jlxFb5gL":false,"ktOFBe9N":{"KTEfbUvj":294}}';
        static::assertSame($expected_json, $actual_json);
        $actual_array = PublicJsonSerializable::jsonToArray($actual_json);
        $expected_array = [
            'GcnIscoB' => 95.31,
            'FsU1looN' => true,
            'jlxFb5gL' => false,
            'ktOFBe9N' => ['KTEfbUvj' => 294]
        ];
        static::assertSame($expected_array, $actual_array);
        $actual = json_decode($actual_json, false, 512, JSON_THROW_ON_ERROR);
        static::assertInstanceOf(stdClass::class, $actual);
        $actual_array2 = PublicJsonSerializable::objToArray($actual);
        static::assertSame($expected_array, $actual_array2);
        $store->reset();
        static::assertSame([], $store->toArray());
    }

    /**
     * Data provider for valid JSON objects.
     * @return array<int, array<int, string|stdClass|array<string, string|int>>>
     */
    public static function provideValidJsonObjects(): array
    {
        $obj1 = '{"2at0q6hz":"3g8Z57oK","GNhrr5BB":9800}';
        return [
            [$obj1],
            [json_decode($obj1, false)],
            [json_decode($obj1, true)]
        ];
    }

    /**
     * Test valid JSON objects to array conversion.
     * @param stdClass|array<string, string|int>|string $obj
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideValidJsonObjects
     */
    public function testValidJsonObjects(array|string|stdClass $obj): void
    {
        $actual_array = PublicJsonSerializable::objToArray($obj);
        $expected_array = [
            '2at0q6hz' => '3g8Z57oK',
            'GNhrr5BB' => 9800
        ];
        static::assertSame($expected_array, $actual_array);
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
        PublicJsonSerializable::$allowedKeys = [
            'L0AJfput',
            'RmHQUA8s'
        ];
        $json = '{"L0AJfput":"eG8B39RG","RmHQUA8s":521}';
        $obj = PublicJsonSerializable::jsonDecode($json);
        static::assertInstanceOf(IJsonSerializable::class, $obj);
        static::assertInstanceOf(JsonSerializable::class, $obj);
        static::assertInstanceOf(PublicJsonSerializable::class, $obj);
        static::assertTrue($obj->has('L0AJfput'));
        static::assertSame('eG8B39RG', $obj->get('L0AJfput'));
        static::assertTrue($obj->has('RmHQUA8s'));
        static::assertSame(521, $obj->get('RmHQUA8s'));
    }

    /**
     * Data provider for invalid JSON.
     * @return array<int, array<int, string>>
     */
    public static function provideInvalidJson(): array
    {
        return [
            [''],
            [' '],
            ['{'],
            ['}'],
            ['{"w1sBz6nE":3501'],
            ['aBtxi4bR'],
        ];
    }

    /**
     * Test decoding invalid JSON.
     * @param string $json
     * @dataProvider provideInvalidJson
     */
    public function testInvalidJsonToArray(string $json): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid JSON! Expected JSON encoded tests\\phpsap\\classes\\helper\\PublicJsonSerializable string!'
        );
        PublicJsonSerializable::jsonToArray($json);
    }

    /**
     * Test decoding invalid JSON objects.
     * @param string $string
     * @dataProvider provideInvalidJson
     */
    public function testInvalidJsonObjectToArray(string $string): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid JSON object! Expected tests\\phpsap\\classes\\helper\\PublicJsonSerializable JSON object or array!'
        );
        PublicJsonSerializable::objToArray($string);
    }
}
