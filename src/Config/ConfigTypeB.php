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
     * @var array
     */
    protected static $configKeys = [
        self::JSON_MSHOST,
        self::JSON_R3NAME,
        self::JSON_GROUP
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
     * Get the name of SAP system, optional; default: destination
     * @return string name of SAP system.
     */
    public function getR3name()
    {
        return $this->get(self::JSON_R3NAME);
    }

    /**
     * Set the name of SAP system, optional; default: destination
     * @param string $r3name The name of the SAP system.
     * @return ConfigTypeB
     */
    public function setR3name($r3name)
    {
        $this->set(self::JSON_R3NAME, $r3name);
        return $this;
    }

    /**
     * Get the host name of the message server.
     * @return string host name of the message server
     */
    public function getMshost()
    {
        return $this->get(self::JSON_MSHOST);
    }

    /**
     * Set the host name of the message server.
     * @param string $mshost The host name of the message server.
     * @return ConfigTypeB
     */
    public function setMshost($mshost)
    {
        $this->set(self::JSON_MSHOST, $mshost);
        return $this;
    }

    /**
     * Get the group name of the application servers, optional; default: PUBLIC.
     * @return string group name of the application servers
     */
    public function getGroup()
    {
        return $this->get(self::JSON_GROUP);
    }

    /**
     * Set the group name of the application servers, optional; default: PUBLIC.
     * @param string $group The group name of the application servers.
     * @return ConfigTypeB
     */
    public function setGroup($group)
    {
        $this->set(self::JSON_GROUP, $group);
        return $this;
    }
}
