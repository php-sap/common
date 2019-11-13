<?php

namespace phpsap\classes\Config;

use phpsap\interfaces\Config\IConfigTypeA;

/**
 * Class phpsap\classes\Config\ConfigTypeA
 *
 * Configure connection parameters for SAP remote function calls using a specific
 * SAP application server (type A).
 *
 * @package phpsap\classes\Config
 * @author  Gregor J.
 * @license MIT
 */
class ConfigTypeA extends ConfigCommon implements IConfigTypeA
{
    /**
     * @var array
     */
    protected static $configKeys = [
        self::JSON_ASHOST,
        self::JSON_SYSNR,
        self::JSON_GWHOST,
        self::JSON_GWSERV
    ];

    /**
     * Get an array of all valid configuration keys and whether they are mandatory.
     * @return array
     */
    public static function getValidConfigKeys()
    {
        return array_merge(parent::getValidConfigKeys(), self::$configKeys);
    }

    /**
     * Get the host name of a specific SAP application server.
     * @return string host name of a specific SAP application server
     */
    public function getAshost()
    {
        return $this->get(self::JSON_ASHOST);
    }

    /**
     * Set the host name of a specific SAP application server.
     * @param string $ashost The host name of a specific SAP application server.
     * @return ConfigTypeA
     */
    public function setAshost($ashost)
    {
        $this->set(self::JSON_ASHOST, $ashost);
        return $this;
    }

    /**
     * Get the SAP system number.
     * @return string SAP system number
     */
    public function getSysnr()
    {
        return $this->get(self::JSON_SYSNR);
    }

    /**
     * Set the SAP system number.
     * @param string $sysnr The SAP system number.
     * @return ConfigTypeA
     */
    public function setSysnr($sysnr)
    {
        $this->set(self::JSON_SYSNR, $sysnr);
        return $this;
    }

    /**
     * optional; default: gateway on application server
     * @return string gateway on application server
     */
    public function getGwhost()
    {
        return $this->get(self::JSON_GWHOST);
    }

    /**
     * optional; default: gateway on application server
     * @param string $gwhost The gateway on the application server.
     * @return ConfigTypeA
     */
    public function setGwhost($gwhost)
    {
        $this->set(self::JSON_GWHOST, $gwhost);
        return $this;
    }

    /**
     * optional; default: gateway on application server
     * @return string gateway on application server
     */
    public function getGwserv()
    {
        return $this->get(self::JSON_GWSERV);
    }

    /**
     * optional; default: gateway on application server
     * @param string $gwserv The gateway on the application server.
     * @return ConfigTypeA
     */
    public function setGwserv($gwserv)
    {
        $this->set(self::JSON_GWSERV, $gwserv);
        return $this;
    }
}
