<?php

namespace tests\phpsap\classes\Util;

use phpsap\exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Exception;
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
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidArgumentException
     */
    public function testInheritance()
    {
        $obj = new PublicJsonSerializable();
        static::assertInstanceOf(\JsonSerializable::class, $obj);
        static::assertInstanceOf(JsonSerializable::class, $obj);
        static::assertSame([], $obj->toArray());
    }

    /**
     * Test the successful storage of data.
     * @throws PHPUnit_Framework_AssertionFailedError
     * @throws InvalidArgumentException
     */
    public function testSuccessfulDataStorage()
    {
        PublicJsonSerializable::$allowedKeys = [
            'bhtWTHMh',
            'GcnIscoB',
            'FsU1looN',
            'jlxFb5gL',
            'ktOFBe9N',
            'fVWlA9ea'
        ];
        $store = new PublicJsonSerializable([
            'bhtWTHMh' => 54247,
            'GcnIscoB' => 95.31,
            'FsU1looN' => true,
            'jlxFb5gL' => false,
            'ktOFBe9N' => ['KTEfbUvj' => 294]
        ]);
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
        $actualJson = json_encode($store);
        $expectedJson = '{"GcnIscoB":95.31,"FsU1looN":true,"jlxFb5gL":false,"ktOFBe9N":{"KTEfbUvj":294}}';
        static::assertSame($expectedJson, $actualJson);
        $actualArray = PublicJsonSerializable::jsonToArray($actualJson);
        $expectedArray = [
            'GcnIscoB' => 95.31,
            'FsU1looN' => true,
            'jlxFb5gL' => false,
            'ktOFBe9N' => ['KTEfbUvj' => 294]
        ];
        static::assertSame($expectedArray, $actualArray);
        $actualArray2 = PublicJsonSerializable::objToArray(json_decode($actualJson, false));
        static::assertSame($expectedArray, $actualArray2);
        $store->reset();
        static::assertSame([], $store->toArray());
    }

    /**
     * Data provider for valid JSON objects.
     * @return array
     */
    public static function provideValidJsonObjects()
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
     * @param stdClass|array|string $obj
     * @dataProvider provideValidJsonObjects
     * @throws InvalidArgumentException
     */
    public function testValidJsonObjects($obj)
    {
        $actualArray = PublicJsonSerializable::objToArray($obj);
        $expectedArray = [
            '2at0q6hz' => '3g8Z57oK',
            'GNhrr5BB' => 9800
        ];
        static::assertSame($expectedArray, $actualArray);
    }

    /**
     * Data provider for invalid keys.
     * @return array
     */
    public static function provideInvalidKeys()
    {
        return [
            [''],
            [' '],
            ['dY1 Us-xO_L5H'],
            ['phoj.hbd.tcf'],
            [true],
            [false],
            [null],
            [new stdClass()],
            [['k6aomsm9vb']]
        ];
    }

    /**
     * Test invalid keys.
     * @param mixed $key
     * @dataProvider provideInvalidKeys
     */
    public function testInvalidKeys($key)
    {
        $store = new PublicJsonSerializable();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid key!');
        $store->has($key);
    }

    /**
     * Test setting an unknown key.
     */
    public function testSettingUnknownKey()
    {
        PublicJsonSerializable::$allowedKeys = [];
        $store = new PublicJsonSerializable();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown key \'OdUoAAih\'!');
        $store->set('OdUoAAih', 4.867);
    }

    /**
     * Test to set an invalid value.
     */
    public function testSetInvalidData()
    {
        PublicJsonSerializable::$allowedKeys = ['AxiBKNAu'];
        $store = new PublicJsonSerializable();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value!');
        $store->set('AxiBKNAu', new stdClass());
    }

    /**
     * Data provider for invalid arrays.
     * @return array
     */
    public static function provideInvalidArrays()
    {
        return [
            ['nB47gijE'],
            [7],
            [5.2],
            [true],
            [false],
            [null],
            [new stdClass()]
        ];
    }

    /**
     * Test to set an invalid value.
     * @param mixed $data
     * @dataProvider provideInvalidArrays
     */
    public function testSetInvalidArrays($data)
    {
        $store = new PublicJsonSerializable();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid array!');
        $store->setMultiple($data);
    }

    /**
     * Test decoding a JSON encoded object.
     * @throws PHPUnit_Framework_Exception
     * @throws InvalidArgumentException
     */
    public function testDecodingObjectFromJson()
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
     * @return array
     */
    public static function provideInvalidJson()
    {
        return [
            [''],
            [' '],
            ['{'],
            ['}'],
            ['{"w1sBz6nE":3501'],
            ['aBtxi4bR'],
            [6598],
            [8.041],
            [true],
            [false],
            [null],
            [new stdClass()],
            [['kSFjubPU' => 14.6]]
        ];
    }

    /**
     * Test decoding invalid JSON.
     * @param mixed $json
     * @dataProvider provideInvalidJson
     */
    public function testInvalidJsonToArray($json)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid JSON! Expected JSON encoded tests\\phpsap\\classes\\helper\\PublicJsonSerializable string!'
        );
        PublicJsonSerializable::jsonToArray($json);
    }

    /**
     * Data provider of invalid JSON objects.
     * @return array
     */
    public static function provideInvalidJsonObjects()
    {
        return [
            [66],
            [753.1],
            [true],
            [false],
            [null]
        ];
    }

    /**
     * Test decoding invalid JSON objects.
     * @param mixed $obj
     * @dataProvider provideInvalidJsonObjects
     */
    public function testInvalidJsonObjectToArray($obj)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid JSON object! Expected tests\\phpsap\\classes\\helper\\PublicJsonSerializable JSON object or array!'
        );
        PublicJsonSerializable::objToArray($obj);
    }
}
