<?php
/**
 * File tests/helper/RemoteFunction.php
 *
 * Helper class extending the abstract function class for testing.
 *
 * @package common
 * @author  Gregor J.
 * @license MIT
 */

namespace tests\phpsap\classes\helper;

use phpsap\classes\AbstractFunction;
use phpsap\classes\RemoteApi;

/**
 * Class tests\phpsap\classes\helper\RemoteFunction
 *
 * Helper class extending the abstract function class for testing.
 *
 * @package tests\phpsap\classes\helper
 * @author  Gregor J.
 * @license MIT
 */
class RemoteFunction extends AbstractFunction
{
    /**
     * @var array|null
     */
    public $results;

    /**
     * @var array
     */
    public static $extractedApi;

    /**
     * Clear remote function call.
     */
    public function __destruct()
    {
    }

    /**
     * Execute the prepared function call.
     * @return array
     * @throws \phpsap\exceptions\ConnectionFailedException
     * @throws \phpsap\exceptions\FunctionCallException
     */
    public function invoke()
    {
        if ($this->results instanceof \Exception) {
            throw new $this->results;
        }
        return $this->results;
    }

    /**
     * Debug function for testing.
     * Retrieves inaccessible variables for inspection.
     * @param string $name
     * @return mixed
     */
    public function debugGet($name)
    {
        if (is_string($name) && isset($this->{$name})) {
            return $this->{$name};
        }
        throw new \LogicException('Unknown attribute!');
    }

    /**
     * Make protected function public for testing.
     * Get a parameter previously defined using setParam()
     * @param string $name
     * @param null   $default
     * @return mixed|null
     */
    public function getParam($name, $default = null)
    {
        return parent::getParam($name, $default);
    }

    /**
     * Get the PHP module remote function ressource/object.
     * @return mixed
     * @throws \phpsap\exceptions\UnknownFunctionException
     */
    protected function getFunction()
    {
        return 'TRBp3hoJ';
    }

    /**
     * Extract the remote function API and return an API description class.
     * @return \phpsap\interfaces\IApi
     */
    public function extractApi()
    {
        return new RemoteApi(static::$extractedApi);
    }
}
