<?php

namespace phpsap\classes\Config;

use InvalidArgumentException;
use phpsap\interfaces\Config\IConfigCommon;

/**
 * Class phpsap\classes\Config\ConfigCommon
 *
 * Configure common connection parameters for SAP remote function calls.
 *
 * @package phpsap\classes\Config
 * @author  Gregor J.
 * @license MIT
 */
abstract class ConfigCommon extends AbstractConfiguration implements IConfigCommon
{
    /**
     * @var array
     */
    protected static $configKeys = [
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
     * Get an array of all valid configuration keys and whether they are mandatory.
     * @return array
     */
    public static function getValidConfigKeys()
    {
        return self::$configKeys;
    }

    /**
     * In case the connection needs to be made through a firewall using a SAPRouter,
     * the parameters are in the following format:
     * /H/hostname/S/portnumber/H/
     * @return string the saprouter
     */
    public function getSaprouter()
    {
        return $this->get(self::JSON_SAPROUTER);
    }

    /**
     * In case the connection needs to be made through a firewall using a SAPRouter,
     * specify the SAPRouter parameters in the following format:
     * /H/hostname/S/portnumber/H/
     * @param string $saprouter The saprouter configuration parameter.
     * @return ConfigCommon
     */
    public function setSaprouter($saprouter)
    {
        if ($saprouter !== null
            && (!is_string($saprouter)
            || !preg_match('~^/H/[a-z\d.\-]+/S/[\d]+/H/$~i', $saprouter))
        ) {
            throw new InvalidArgumentException(
                'Expected SAPROUTER to be in following format: '
                . '/H/<hostname>/S/<portnumber>/H/'
            );
        }
        $this->set(self::JSON_SAPROUTER, $saprouter);
        return $this;
    }

    /**
     * Get the trace level (0-3). See constants TRACE_*.
     * @return int the trace level
     */
    public function getTrace()
    {
        return $this->get(self::JSON_TRACE);
    }

    /**
     * Set the trace level (0-3). See constants TRACE_*.
     * @param int $trace The trace level.
     * @return ConfigCommon
     */
    public function setTrace($trace)
    {
        if ($trace !== null
            && (!is_int($trace) || $trace > self::TRACE_FULL || $trace < self::TRACE_OFF)
        ) {
            throw new InvalidArgumentException(
                'The trace level can only be 0-3!'
            );
        }
        $this->set(self::JSON_TRACE, $trace);
        return $this;
    }

    /**
     * Only needed it if you want to connect to a non-Unicode backend using a
     * non-ISO-Latin-1 user name or password. The RFC library will then use that
     * codepage for the initial handshake, thus preserving the characters in
     * username/password.
     * @return int the codepage
     */
    public function getCodepage()
    {
        return $this->get(self::JSON_CODEPAGE);
    }

    /**
     * Only needed it if you want to connect to a non-Unicode backend using a
     * non-ISO-Latin-1 user name or password. The RFC library will then use that
     * codepage for the initial handshake, thus preserving the characters in
     * username/password.
     * @param int $codepage The codepage.
     * @return ConfigCommon
     */
    public function setCodepage($codepage)
    {
        $this->set(self::JSON_CODEPAGE, $codepage);
        return $this;
    }

    /**
     * Get the username to use for authentication.
     * @return string the username
     */
    public function getUser()
    {
        return $this->get(self::JSON_USER);
    }

    /**
     * Set the username to use for authentication.
     * @param string $user The username.
     * @return ConfigCommon
     */
    public function setUser($user)
    {
        $this->set(self::JSON_USER, $user);
        return $this;
    }

    /**
     * Get the password to use for authentication.
     * @return string the password
     */
    public function getPasswd()
    {
        return $this->get(self::JSON_PASSWD);
    }

    /**
     * Get the password to use for authentication.
     * @param string $passwd The password.
     * @return ConfigCommon
     */
    public function setPasswd($passwd)
    {
        $this->set(self::JSON_PASSWD, $passwd);
        return $this;
    }

    /**
     * Get the destination in RfcOpen.
     * @return string Get the destination in RfcOpen.
     */
    public function getClient()
    {
        return $this->get(self::JSON_CLIENT);
    }

    /**
     * Set the destination in RfcOpen.
     * @param string $client The destination in RfcOpen.
     * @return ConfigCommon
     */
    public function setClient($client)
    {
        $this->set(self::JSON_CLIENT, $client);
        return $this;
    }

    /**
     * Get the logon Language.
     * @return string The logon language.
     */
    public function getLang()
    {
        return $this->get(self::JSON_LANG);
    }

    /**
     * Set the logon Language.
     * @param string $lang The logon language.
     * @return ConfigCommon
     */
    public function setLang($lang)
    {
        if ($lang !== null
            && (!is_string($lang) || !preg_match('~^[A-Z]{2}$~', $lang))
        ) {
            throw new InvalidArgumentException(
                'Expected two letter country code as language!'
            );
        }
        $this->set(self::JSON_LANG, $lang);
        return $this;
    }

    /**
     * Get the destination in RfcOpenConnection.
     * @return string the logon language
     */
    public function getDest()
    {
        return $this->get(self::JSON_DEST);
    }

    /**
     * Set the destination in RfcOpenConnection.
     * @param string $dest The destination in RfcOpenConnection.
     * @return ConfigCommon
     */
    public function setDest($dest)
    {
        $this->set(self::JSON_DEST, $dest);
        return $this;
    }
}
