<?php

namespace phpsap\classes\Config;

use phpsap\exceptions\IncompleteConfigException;
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
     * @var array Allowed JsonSerializable keys to set values for.
     */
    protected static $allowedKeys = [
        self::JSON_ASHOST,
        self::JSON_SYSNR,
        self::JSON_GWHOST,
        self::JSON_GWSERV,
        self::JSON_USER,
        self::JSON_PASSWD,
        self::JSON_CLIENT,
        self::JSON_SAPROUTER,
        self::JSON_TRACE,
        self::JSON_LANG,
        self::JSON_DEST,
        self::JSON_CODEPAGE
    ];

    /**
     * Get the host name of a specific SAP application server.
     * @return string The hostname of a specific SAP application server.
     * @throws \phpsap\exceptions\IncompleteConfigException
     */
    public function getAshost(): string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        if (($result = $this->get(self::JSON_ASHOST)) === null) {
            throw new IncompleteConfigException(sprintf(
                'Configuration is missing mandatory key %s!',
                self::JSON_ASHOST
            ));
        }
        return $result;
    }

    /**
     * Set the host name of a specific SAP application server.
     * @param string $ashost The hostname of a specific SAP application server.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setAshost($ashost): IConfigTypeA
    {
        $this->set(self::JSON_ASHOST, $ashost);
        return $this;
    }

    /**
     * Get the SAP system number.
     * @return string The SAP system number.
     * @throws \phpsap\exceptions\IncompleteConfigException
     */
    public function getSysnr(): string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        if (($result = $this->get(self::JSON_SYSNR)) === null) {
            throw new IncompleteConfigException(sprintf(
                'Configuration is missing mandatory key %s!',
                self::JSON_SYSNR
            ));
        }
        return $result;
    }

    /**
     * Set the SAP system number.
     * @param string $sysnr The SAP system number.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setSysnr($sysnr): IConfigTypeA
    {
        $this->set(self::JSON_SYSNR, $sysnr);
        return $this;
    }

    /**
     * Get the gateway host on the application server.
     * @return string|null The gateway host or NULL in case no gateway host has been defined.
     */
    public function getGwhost(): ?string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_GWHOST);
    }

    /**
     * Set the gateway host on the application server.
     * @param string $gwhost The gateway host.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setGwhost($gwhost): IConfigTypeA
    {
        $this->set(self::JSON_GWHOST, $gwhost);
        return $this;
    }

    /**
     * Get the gateway service on the application server.
     * @return string|null The gateway service or NULL in case no gateway service has been defined.
     */
    public function getGwserv(): ?string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_GWSERV);
    }

    /**
     * Get the gateway service on the application server.
     * @param string $gwserv The gateway service on the application server.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setGwserv($gwserv): IConfigTypeA
    {
        $this->set(self::JSON_GWSERV, $gwserv);
        return $this;
    }
}
