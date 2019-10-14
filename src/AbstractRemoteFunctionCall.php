<?php
/**
 * File src/AbstractRemoteFunctionCall.php
 *
 * PHP/SAP proxy class for SAP remote function calls.
 *
 * @package common
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\classes;

use phpsap\interfaces\IConfig;
use phpsap\interfaces\IFunction;

/**
 * Class Tests\phpsap\saprfc\AbstractRemoteFunctionCall
 *
 * Abstract class handling a PHP/SAP connection and remote function.
 *
 * @package phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
abstract class AbstractRemoteFunctionCall implements IFunction
{
    /**
     * @var \phpsap\interfaces\IConnection
     */
    protected $connection;

    /**
     * @var string The connection ID of the current SAP remote connection.
     */
    protected $connectionId;

    /**
     * @var \phpsap\classes\AbstractFunction
     */
    protected $function;

    /**
     * Initialize the function call, using the configuration.
     * @param \phpsap\interfaces\IConfig $config
     */
    public function __construct(IConfig $config)
    {
        //create a new instance of the connection without actually connecting
        $this->connection = $this->createConnectionInstance($config);
    }

    /**
     * Remove the function instance.
     * @return \phpsap\classes\AbstractRemoteFunctionCall $this
     */
    public function reset()
    {
        $this->function = null;
        return $this;
    }

    /**
     * Set function call parameter.
     * @param string                           $name
     * @param array|bool|float|int|string|null $value
     * @return \phpsap\classes\AbstractRemoteFunctionCall $this
     * @throws \phpsap\interfaces\exceptions\IConnectionFailedException
     * @throws \phpsap\interfaces\exceptions\IUnknownFunctionException
     */
    public function setParam($name, $value)
    {
        $this->getFunction()
            ->setParam($name, $value);
        return $this;
    }

    /**
     * Invoke SAP remote function call.
     * @param null|array $params Optional parameter array.
     * @return array result.
     * @throws \phpsap\interfaces\exceptions\IConnectionFailedException
     * @throws \phpsap\interfaces\exceptions\IUnknownFunctionException
     * @throws \phpsap\exceptions\FunctionCallException
     */
    public function invoke($params = null)
    {
        //invoke the remove function call
        return $this->getFunction()
            ->invoke($params);
    }

    /**
     * Get the function instance.
     * @return \phpsap\classes\AbstractFunction
     * @throws \phpsap\interfaces\exceptions\IConnectionFailedException
     * @throws \phpsap\interfaces\exceptions\IUnknownFunctionException
     */
    protected function getFunction()
    {
        if ($this->function === null) {
            $this->function = $this->connection->prepareFunction($this->getName());
        }
        return $this->function;
    }

    /**
     * Get set function parameters
     * @param null $params
     * @return array|null
     * @throws \phpsap\interfaces\exceptions\IConnectionFailedException
     * @throws \phpsap\interfaces\exceptions\IUnknownFunctionException
     */
    public function getParams($params = null)
    {
        $params = $this->getFunction()->getParamList($params);
        return $params;
    }

    /**
     * The SAP remote function name.
     * @return string
     */
    abstract public function getName();

    /**
     * Create a connection instance using the given config.
     * @param \phpsap\interfaces\IConfig $config
     * @return \phpsap\interfaces\IConnection
     */
    abstract protected function createConnectionInstance(IConfig $config);
}
