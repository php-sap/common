<?php

namespace tests\phpsap\classes\Api;

use phpsap\classes\Api\Element;
use phpsap\classes\Api\Table;
use phpsap\classes\Api\Value;
use phpsap\interfaces\Api\IArray;
use phpsap\interfaces\Api\IElement;
use phpsap\interfaces\Api\IValue;

/**
 * Class tests\phpsap\classes\Api\TableTest
 * @package tests\phpsap\classes\Api
 * @author  Gregor J.
 * @license MIT
 */
class TableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the constructor and the inherited classes and interfaces.
     */
    public function testConstructorAndInheritance()
    {
        $table = new Table('cpfNGceT', true, [
            new Element(Element::TYPE_STRING, 'QoTyE1xK')
        ]);
        //heritage
        static::assertInstanceOf(\JsonSerializable::class, $table);
        static::assertInstanceOf(IArray::class, $table);
        static::assertInstanceOf(Table::class, $table);
        static::assertInstanceOf(IValue::class, $table);
        static::assertInstanceOf(Value::class, $table);
        static::assertInstanceOf(IElement::class, $table);
        static::assertInstanceOf(Element::class, $table);
        //basic in-out
        static::assertSame('cpfNGceT', $table->getName());
        static::assertSame(IArray::DIRECTION_TABLE, $table->getDirection());
        static::assertTrue($table->isOptional());
        static::assertSame(IArray::TYPE_ARRAY, $table->getType());
        //test members
        $members = $table->getMembers();
        static::assertInternalType('array', $members);
        static::assertCount(1, $members);
        foreach ($members as $member) {
            /**
             * @var Element $member
             */
            static::assertInstanceOf(Element::class, $member);
            static::assertSame(IElement::TYPE_STRING, $member->getType());
            static::assertSame('QoTyE1xK', $member->getName());
        }
    }

    /**
     * Test non-array members.
     * @param mixed $members
     * @dataProvider \tests\phpsap\classes\Api\StructTest::provideNonArrays()
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected API table members to be in an array!
     */
    public function testNonArrayMembers($members)
    {
        new Table('ObFY2MbO', true, $members);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected API table members to be instances of IElement!
     */
    public function testNonIElementMembers()
    {
        new Table('XPn2Pf2n', true, [new \stdClass()]);
    }

    /**
     * Test typecasting of table input.
     */
    public function testTableCast()
    {
        $table = new Table('kdIriJOc', true, [
            new Element(Element::TYPE_BOOLEAN, '58J0oSzo'),
            new Element(Element::TYPE_INTEGER, '1U2pHwPS'),
            new Element(Element::TYPE_FLOAT, 'jKPjRoH5'),
            new Element(Element::TYPE_STRING, 'aE7iJDWQ')
        ]);
        $raw = [
            [
                '58J0oSzo' => '0',
                '1U2pHwPS' => '351',
                'jKPjRoH5' => '2.8',
                'aE7iJDWQ' => 987257,
                'copU1Tlu' => '2YQTOpvn' //This element is not defined in the API ...
            ],
            [
                '58J0oSzo' => '1',
                '1U2pHwPS' => '81',
                'jKPjRoH5' => '6.82',
                'aE7iJDWQ' => 23.8,
                'copU1Tlu' => '4Qtlfmbh' //This element is not defined in the API ...
            ]
        ];
        $expected = [
            [
                '58J0oSzo' => false,
                '1U2pHwPS' => 351,
                'jKPjRoH5' => 2.8,
                'aE7iJDWQ' => '987257',
                'copU1Tlu' => '2YQTOpvn' //... therefore it stays untouched.
            ],
            [
                '58J0oSzo' => true,
                '1U2pHwPS' => 81,
                'jKPjRoH5' => 6.82,
                'aE7iJDWQ' => '23.8',
                'copU1Tlu' => '4Qtlfmbh' //... therefore it stays untouched.
            ]
        ];
        $actual = $table->cast($raw);
        static::assertSame($expected, $actual);
    }

    /**
     * Test casting raw data with an element missing.
     * @expectedException \phpsap\exceptions\ArrayElementMissingException
     * @expectedExceptionMessage Element IlRdvEQp in table MLOWwQXa is missing!
     */
    public function testTableCastMissingElement()
    {
        $table = new Table('MLOWwQXa', true, [
            new Element(Element::TYPE_STRING, 'IlRdvEQp')
        ]);
        $raw = [
            [
                'dTAcNC0q' => '6NdXXpA4',
                '4Bxn9DJV' => '250'
            ],
            [
                'dTAcNC0q' => 'SDzd9Cy5',
                '4Bxn9DJV' => '311'
            ]
        ];
        $table->cast($raw);
    }

    /**
     * Test JSON decode.
     */
    public function testJsonDecode()
    {
        $json = '{"type":"array","name":"wWnIOYQc","direction":"table",'
                .'"optional":false,"members":[{"type":"bool","name":"MievIEPs"}]}';
        $element = Table::jsonDecode($json);
        static::assertInstanceOf(Table::class, $element);
        static::assertSame(IArray::TYPE_ARRAY, $element->getType());
        static::assertSame('wWnIOYQc', $element->getName());
        static::assertSame(IArray::DIRECTION_TABLE, $element->getDirection());
        static::assertFalse($element->isOptional());
        static::assertInternalType('array', $element->getMembers());
        $members = $element->getMembers();
        foreach ($members as $member) {
            /**
             * @var \phpsap\interfaces\Api\IElement $member
             */
            static::assertSame(IArray::TYPE_BOOLEAN, $member->getType());
            static::assertSame('MievIEPs', $member->getName());
        }
    }

    /**
     * Test JSON decoding on invalid parameters.
     * @param mixed $json
     * @dataProvider \tests\phpsap\classes\Api\ApiElementTest::provideInvalidJson()
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON!
     */
    public function testInvalidJson($json)
    {
        Table::jsonDecode($json);
    }

    /**
     * Test JSON decoding on incomplete JSON objects.
     * @param string $json
     * @dataProvider \tests\phpsap\classes\Api\StructTest::provideIncompleteJsonObjects()
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON: API Table is missing
     */
    public function testIncompleteJson($json)
    {
        Table::jsonDecode($json);
    }

    /**
     * Data provider for JSON objects with invalid type value.
     * @return array
     */
    public static function provideJsonDecodeInvalidDirection()
    {
        return [
            ['{"type":"array","name":"3iumcKfi","direction":"input","optional":true,'
             .'"members":[{"type":"int","name":"R7atJFrf"}]}'],
            ['{"type":"array","name":"0Uha2w0d","direction":"output","optional":true,'
             .'"members":[{"type":"int","name":"DhO5JG4n"}]}']
        ];
    }

    /**
     * Test JSON decode objects with invalid type value.
     * @param string $json
     * @dataProvider provideJsonDecodeInvalidDirection
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON: API Table direction is not table!
     */
    public function testJsonDecodeInvalidDirection($json)
    {
        Table::jsonDecode($json);
    }

    /**
     * Data provider for JSON objects with invalid type value.
     * @return array
     */
    public static function provideJsonDecodeInvalidType()
    {
        return [
            ['{"type":"bool","name":"azc5s1Nb","direction":"table","optional":true,'
             .'"members":[{"type":"int","name":"REFsrhPg"}]}'],
            ['{"type":"int","name":"176PZCKM","direction":"table","optional":true,'
             .'"members":[{"type":"int","name":"BLXWiyhY"}]}'],
            ['{"type":"float","name":"hACHkDDV","direction":"table","optional":true,'
             .'"members":[{"type":"int","name":"CsY4Od9s"}]}'],
            ['{"type":"string","name":"sDbJFlkB","direction":"table",'
             .'"optional":true,"members":[{"type":"int","name":"bVhCMbhQ"}]}']
        ];
    }

    /**
     * Test JSON decode objects with invalid type value.
     * @param string $json
     * @dataProvider provideJsonDecodeInvalidType
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON: API Table type is not an array!
     */
    public function testJsonDecodeInvalidType($json)
    {
        Table::jsonDecode($json);
    }

    /**
     * Data provider for JSON objects with invalid members.
     * @return array
     */
    public static function provideJsonDecodeInvalidMembers()
    {
        return [
            ['{"type":"array","name":"96JeBc1U","direction":"table",'
             .'"optional":true,"members":13}'],
            ['{"type":"array","name":"X1Lw0efh","direction":"table",'
             .'"optional":true,"members":8.82}'],
            ['{"type":"array","name":"AdrbBd9G","direction":"table",'
             .'"optional":true,"members":"AbgHgWhx"}'],
            ['{"type":"array","name":"RvU15SUm","direction":"table",'
             .'"optional":true,"members":true}'],
            ['{"type":"array","name":"7LQXw6IT","direction":"table",'
             .'"optional":true,"members":false}'],
        ];
    }

    /**
     * Test JSON decode objects with invalid members.
     * @param string $json
     * @dataProvider provideJsonDecodeInvalidMembers
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid JSON: API Table members are not an array!
     */
    public function testJsonDecodeInvalidMembers($json)
    {
        Table::jsonDecode($json);
    }
}
