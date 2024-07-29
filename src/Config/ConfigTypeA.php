<?php

declare(strict_types=1);

namespace phpsap\classes\Config;

use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\InvalidArgumentException;
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
    protected static array $allowedKeys = [
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
     * @throws InvalidArgumentException
     */
    public function getAshost(): string
    {
        return $this->get(self::JSON_ASHOST);
    }

    /**
     * Set the host name of a specific SAP application server.
     * @param string $ashost The hostname of a specific SAP application server.
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setAshost(string $ashost): IConfigTypeA
    {
        $this->set(self::JSON_ASHOST, $ashost);
        return $this;
    }

    /**
     * Get the SAP system number.
     * @return string The SAP system number.
     * @throws InvalidArgumentException
     */
    public function getSysnr(): string
    {
        return $this->get(self::JSON_SYSNR);
    }

    /**
     * Set the SAP system number.
     * @param string $sysnr The SAP system number.
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setSysnr(string $sysnr): IConfigTypeA
    {
        $this->set(self::JSON_SYSNR, $sysnr);
        return $this;
    }

    /**
     * Get the gateway host on the application server.
     * @return string|null The gateway host or NULL in case no gateway host has been defined.
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     */
    public function setGwhost(string $gwhost): IConfigTypeA
    {
        $this->set(self::JSON_GWHOST, $gwhost);
        return $this;
    }

    /**
     * Get the gateway service on the application server.
     * @return string|null The gateway service or NULL in case no gateway service has been defined.
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     */
    public function setGwserv(string $gwserv): IConfigTypeA
    {
        $this->set(self::JSON_GWSERV, $gwserv);
        return $this;
    }
}
