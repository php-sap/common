<?php
/**
 * File src/AbstractFunction.php
 *
 * PHP/SAP abstract function class.
 *
 * @package common
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\classes;

use phpsap\interfaces\IApi;
use phpsap\interfaces\IFunction;

/**
 * Class phpsap\classes\AbstractFunction
 *
 * Abstract class to manage a single PHP/SAP remote function instance.
 *
 * @package phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
abstract class AbstractFunction implements IFunction
{
    /**
     * @var mixed PHP module connection ressource/object
     */
    protected $connection;

    /**
     * @var mixed PHP module remote function ressource/object
     */
    protected $function;

    /**
     * @var string remote function name
     */
    protected $name;

    /**
     * @var array remote function parameters
     */
    protected $params;

    /**
     * @var \phpsap\interfaces\IApi The remote function API.
     */
    protected $api;

    /**
     * Initialize this class with a connection instance and the function name.
     * @param mixed $connection Connection resource/object
     * @param string $name
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    public function __construct($connection, $name)
    {
        $this->connection = $connection;
        $this->name = $name;
        $this->reset();
        $this->function = $this->getFunction();
    }

    /**
     * Get the function name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Manually set the remote function API (e.g. from cache).
     * @param \phpsap\interfaces\IApi $api
     * @return \phpsap\interfaces\IFunction
     */
    public function setApi(IApi $api)
    {
        $this->api = $api;
        return $this;
    }

    /**
     * Retrieve the remote function API.
     * Either the API has been set using setApi() or the API will be extracted from
     * SAP.
     * @return \phpsap\interfaces\IApi
     */
    public function getApi()
    {
        if ($this->api === null) {
            $this->api = $this->extractApi();
        }
        return $this->api;
    }

    /**
     * Remove all parameters that have been set and start over.
     * @return \phpsap\classes\AbstractFunction $this
     */
    public function reset()
    {
        $this->params = [];
        return $this;
    }

    /**
     * Set function call parameter.
     * @param string                           $name
     * @param array|string|float|int|bool|null $value
     * @return \phpsap\classes\AbstractFunction $this
     * @throws \InvalidArgumentException
     */
    public function setParam($name, $value)
    {
        if (!is_string($name) || empty($name)) {
            throw new \InvalidArgumentException(sprintf(
                'Expected function %s invoke parameter name to be string',
                $this->getName()
            ));
        }
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Get all set parameters.
     * @return array Associative array of all parameters that have been set.
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get a parameter previously defined using setParam().
     * In case the requested parameter has not been set, return the defined default value.
     * @param string $name
     * @param null   $default
     * @return mixed|null
     */
    protected function getParam($name, $default = null)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }
        return $default;
    }

    /**
     * Extract the remote function API and return an API description class.
     * @return \phpsap\interfaces\IApi
     */
    abstract public function extractApi();

    /**
     * Invoke the prepared function call.
     * @return array
     * @throws \InvalidArgumentException
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\FunctionCallException
     */
    abstract public function invoke();

    /**
     * Clear remote function call.
     */
    abstract public function __destruct();

    /**
     * Get the PHP module remote function ressource/object.
     * @return mixed
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    abstract protected function getFunction();
}
