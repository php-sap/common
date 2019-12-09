<?php

namespace phpsap\classes\Config;

use phpsap\interfaces\Config\IConfigTypeB;

/**
 * Class phpsap\classes\Config\ConfigTypeB
 *
 * Configure connection parameters for SAP remote function calls using load
 * balancing (type B).
 *
 * @package phpsap\classes\Config
 * @author  Gregor J.
 * @license MIT
 */
class ConfigTypeB extends ConfigCommon implements IConfigTypeB
{
    /**
     * @var array Allowed JsonSerializable keys to set values for.
     */
    protected static $allowedKeys = [
        self::JSON_MSHOST,
        self::JSON_R3NAME,
        self::JSON_GROUP,
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
     * Get the host name of the message server.
     * @return string host name of the message server
     */
    public function getMshost()
    {
        /**
         * InvalidArgumentException will never be thrown, because of the static
         * definition of the key.
         */
        return $this->get(self::JSON_MSHOST);
    }

    /**
     * Set the host name of the message server.
     * @param string $mshost The host name of the message server.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setMshost($mshost)
    {
        $this->set(self::JSON_MSHOST, $mshost);
        return $this;
    }

    /**
     * Get the name of SAP system, optional; default: destination
     * @return string name of SAP system.
     */
    public function getR3name()
    {
        /**
         * InvalidArgumentException will never be thrown, because of the static
         * definition of the key.
         */
        return $this->get(self::JSON_R3NAME);
    }

    /**
     * Set the name of SAP system, optional; default: destination
     * @param string $r3name The name of the SAP system.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setR3name($r3name)
    {
        $this->set(self::JSON_R3NAME, $r3name);
        return $this;
    }

    /**
     * Get the group name of the application servers, optional; default: PUBLIC.
     * @return string group name of the application servers
     */
    public function getGroup()
    {
        /**
         * InvalidArgumentException will never be thrown, because of the static
         * definition of the key.
         */
        return $this->get(self::JSON_GROUP);
    }

    /**
     * Set the group name of the application servers, optional; default: PUBLIC.
     * @param string $group The group name of the application servers.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setGroup($group)
    {
        $this->set(self::JSON_GROUP, $group);
        return $this;
    }
}
