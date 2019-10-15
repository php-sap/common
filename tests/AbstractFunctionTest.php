<?php
/**
 * File tests/AbstractFunctionTest.php
 *
 * Test the abstract function class.
 *
 * @package common
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\classes;

use phpsap\classes\AbstractFunction;
use phpsap\exceptions\FunctionCallException;
use phpsap\interfaces\IFunction;
use tests\phpsap\classes\helper\RemoteFunction;

/**
 * Class tests\phpsap\classes\AbstractFunctionTest
 *
 * Test the abstract function class.
 *
 * @package tests\phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
class AbstractFunctionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test class inheritance.
     */
    public function testInheritance()
    {
        $resource = 'LuV57SIb';
        $function = new RemoteFunction($resource, 'my_cool_function');
        static::assertInstanceOf(IFunction::class, $function);
        static::assertInstanceOf(AbstractFunction::class, $function);
        static::assertInstanceOf(RemoteFunction::class, $function);
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $resource = 'tt2YWqeX';
        $function = new RemoteFunction($resource, '7Q81mA0M');
        static::assertInstanceOf(RemoteFunction::class, $function);
        static::assertSame($resource, $function->debugGet('connection'));
        static::assertSame('7Q81mA0M', $function->getName());
        static::assertSame([], $function->getParams());
    }

    public function testSetParam()
    {
        $resource = 'grFGmOH4';
        $function = new RemoteFunction($resource, 'FcWOSDAa');
        $function->setParam('QG5ie8PS', 'JNmHX42z');
        static::assertSame(['QG5ie8PS' => 'JNmHX42z'], $function->getParams());
    }

    /**
     * Data provider for invalid parameter names.
     * @return array
     */
    public static function invalidParamNames()
    {
        return [
            [''],
            [123],
            [45.6],
            [true],
            [false],
            [null],
            [new \stdClass()],
            [[]]
        ];
    }

    /**
     * Test exception thrown in case an invalid parameter name has been used.
     * @param mixed $name
     * @dataProvider invalidParamNames
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expected function Nfa78UkL invoke parameter name to be string
     */
    public function testInvalidParamNames($name)
    {
        $resource = 'X2X1ZZXs';
        $function = new RemoteFunction($resource, 'Nfa78UkL');
        $function->setParam($name, 'nkH0Ovrp');
    }

    /**
     * Test retrieving params.
     */
    public function testGetParam()
    {
        $resource = 'NyFvGBzW';
        $function = new RemoteFunction($resource, 'Y3DuHqJk');
        $function->setParam('aJMLztf1', 'C4bFA5fA')
            ->setParam('NpvS2eyw', 542);
        static::assertSame('C4bFA5fA', $function->getParam('aJMLztf1'));
        static::assertSame(542, $function->getParam('NpvS2eyw'));
        static::assertSame(7.8, $function->getParam('Hzc4KxPn', 7.8));
        $function->reset();
        static::assertSame([], $function->getParams());
    }

    /**
     * Test invoking a function call.
     */
    public function testInvoke()
    {
        $resource = 'nP24u6HZ';
        $function = new RemoteFunction($resource, 'HPOjsyFm');
        $function->results = ['tnvqmfNU' => 91];
        $result = $function->invoke();
        static::assertSame(['tnvqmfNU' => 91], $result);
    }

    /**
     * Test exception upon invoke.
     * @expectedException \phpsap\exceptions\FunctionCallException
     */
    public function testExceptionInvoke()
    {
        $resource = 'TgJ4DSWX';
        $function = new RemoteFunction($resource, 'MfGlebYV');
        $function->results = new FunctionCallException('8hQuRt80');
        $function->invoke();
    }
}
