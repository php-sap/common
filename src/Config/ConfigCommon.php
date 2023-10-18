<?php

namespace phpsap\classes\Config;

use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\InvalidArgumentException;
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
     * @var array Allowed JsonSerializable keys to set values for.
     */
    protected static $allowedKeys = [
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
     * Get the username to use for authentication.
     * @return string
     * @throws \phpsap\exceptions\IncompleteConfigException
     */
    public function getUser()
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        if (($result = $this->get(self::JSON_USER)) === null) {
            throw new IncompleteConfigException(sprintf(
                'Configuration is missing mandatory key %s!',
                self::JSON_USER
            ));
        }
        return $result;
    }

    /**
     * Set the username to use for authentication.
     * @param string $user The username.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setUser($user)
    {
        $this->set(self::JSON_USER, $user);
        return $this;
    }

    /**
     * Get the password to use for authentication.
     * @return string
     * @throws \phpsap\exceptions\IncompleteConfigException
     */
    public function getPasswd()
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        if (($result = $this->get(self::JSON_PASSWD)) === null) {
            throw new IncompleteConfigException(sprintf(
                'Configuration is missing mandatory key %s!',
                self::JSON_PASSWD
            ));
        }
        return $result;
    }

    /**
     * Get the password to use for authentication.
     * @param string $passwd The password.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setPasswd($passwd)
    {
        $this->set(self::JSON_PASSWD, $passwd);
        return $this;
    }

    /**
     * Get the client.
     * @return string
     * @throws \phpsap\exceptions\IncompleteConfigException
     */
    public function getClient()
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        if (($result = $this->get(self::JSON_CLIENT)) === null) {
            throw new IncompleteConfigException(sprintf(
                'Configuration is missing mandatory key %s!',
                self::JSON_CLIENT
            ));
        }
        return $result;
    }

    /**
     * Set the client.
     * @param string $client The client.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setClient($client)
    {
        $this->set(self::JSON_CLIENT, $client);
        return $this;
    }

    /**
     * In case the connection needs to be made through a firewall using a SAPRouter,
     * the parameters are in the following format:
     * /H/hostname/S/portnumber/H/
     * @return string|null The saprouter or NULL in case the saprouter hasn't been set.
     */
    public function getSaprouter()
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_SAPROUTER);
    }

    /**
     * In case the connection needs to be made through a firewall using a SAPRouter,
     * specify the SAPRouter parameters in the following format:
     * /H/hostname/S/portnumber/H/
     * @param string $saprouter The saprouter configuration parameter.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setSaprouter($saprouter)
    {
        if (
            $saprouter !== null
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
     * @return int|null The trace level or NULL in case the trace level hasn't been set.
     */
    public function getTrace()
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_TRACE);
    }

    /**
     * Set the trace level (0-3). See constants TRACE_*.
     * @param int $trace The trace level.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setTrace($trace)
    {
        if (
            $trace !== null
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
     * @return int|null The codepage or NULL in case the codepage hasn't been set.
     */
    public function getCodepage()
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_CODEPAGE);
    }

    /**
     * Only needed it if you want to connect to a non-Unicode backend using a
     * non-ISO-Latin-1 user name or password. The RFC library will then use that
     * codepage for the initial handshake, thus preserving the characters in
     * username/password.
     * @param int $codepage The codepage.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setCodepage($codepage)
    {
        $this->set(self::JSON_CODEPAGE, $codepage);
        return $this;
    }

    /**
     * Get the logon Language.
     * @return string|null The logon language or NULL in case the logon language hasn't been set.
     */
    public function getLang()
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_LANG);
    }

    /**
     * Set the logon language.
     * @param string $lang The logon language.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setLang($lang)
    {
        if (
            $lang !== null
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
     * @return string|null The destination or NULL in case the destination hasn't been set.
     */
    public function getDest()
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_DEST);
    }

    /**
     * Set the destination in RfcOpenConnection.
     * @param string $dest The destination in RfcOpenConnection.
     * @return $this
     * @throws \phpsap\exceptions\InvalidArgumentException
     */
    public function setDest($dest)
    {
        $this->set(self::JSON_DEST, $dest);
        return $this;
    }
}
