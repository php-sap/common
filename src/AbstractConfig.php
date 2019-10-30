<?php
/**
 * File src/AbstractConfig.php
 *
 * Basic configuration.
 *
 * @package common
 * @author  Gregor J.
 * @license MIT
 */

namespace phpsap\classes;

use InvalidArgumentException;
use phpsap\interfaces\IConfig;

/**
 * Class phpsap\classes\AbstractConfig
 *
 * Abstract class to configure basic connection parameters for SAP remote function
 * calls, that are common to both connection types (A, and B).
 *
 * @package phpsap\classes
 * @author  Gregor J.
 * @license MIT
 */
abstract class AbstractConfig extends AbstractConfigContainer implements IConfig
{
    /**
     * Get the username.
     * @return string the username
     */
    public function getUser()
    {
        return $this->get('user');
    }

    /**
     * Set the username.
     * @param string $value the username
     */
    protected function setUser($value)
    {
        $this->set('user', (string)$value);
    }

    /**
     * Get the password.
     * @return string the password
     */
    public function getPasswd()
    {
        return $this->get('passwd');
    }

    /**
     * Set the password.
     * @param string $value the password
     */
    protected function setPasswd($value)
    {
        $this->set('passwd', (string)$value);
    }

    /**
     * Get the Client to which to logon.
     * @return string the client
     */
    public function getClient()
    {
        return $this->get('client');
    }

    /**
     * Set the Client to which to logon.
     * @param string $value the client
     */
    protected function setClient($value)
    {
        $this->set('client', (string)$value);
    }

    /**
     * Get the logon language.
     * @return string the logon language
     */
    public function getLang()
    {
        return $this->get('lang');
    }

    /**
     * Set the logon language.
     * @param string $value the logon language
     * @throws \InvalidArgumentException
     */
    protected function setLang($value)
    {
        if (!is_string($value)
            || !preg_match('~^[A-Z]{2}$~', $value, $match)
        ) {
            throw new InvalidArgumentException(
                'Expected two letter country code as language.'
            );
        }
        $this->set('lang', (string)$match[0]);
    }

    /**
     * If the connection needs to be made through a firewall using a SAPRouter.
     * @return string the saprouter
     */
    public function getSaprouter()
    {
        return $this->get('saprouter');
    }

    /**
     * If the connection needs to be made through a firewall using a SAPRouter,
     * specify the SAPRouter parameters in the following format:
     * /H/hostname/S/portnumber/H/
     * @param string the saprouter
     */
    public function setSaprouter($value)
    {
        $this->set('saprouter', (string)$value);
    }

    /**
     * Get the trace level (0-3)
     * @return int the trace level
     */
    public function getTrace()
    {
        return $this->get('trace');
    }

    /**
     * Set the trace level (0-3)
     * @param int|string the trace level
     * @throws \InvalidArgumentException
     */
    public function setTrace($value)
    {
        if ((!is_int($value) && !is_string($value))
           || !preg_match('~^[0-3]$~', $value, $match)
        ) {
            throw new InvalidArgumentException(
                'The trace level can only be 0-3.'
            );
        }
        $this->set('trace', (int)$match[0]);
    }

    /**
     * Only needed it if you want to connect to a non-Unicode backend using a
     * non-ISO-Latin-1 user name or password. The RFC library will then use that
     * codepage for the initial handshake, thus preserving the characters in
     * username/password.
     *
     * @return int the codepage
     */
    public function getCodepage()
    {
        return $this->get('codepage');
    }

    /**
     * Only needed it if you want to connect to a non-Unicode backend using a
     * non-ISO-Latin-1 user name or password. The RFC library will then use that
     * codepage for the initial handshake, thus preserving the characters in
     * username/password.
     *
     * @param int the codepage
     */
    public function setCodepage($value)
    {
        $this->set('codepage', (int)$value);
    }

    /**
     * Get the destination in RfcOpenConnection.
     * @return string the destination
     */
    public function getDest()
    {
        return $this->get('dest');
    }

    /**
     * Set the destination in RfcOpenConnection.
     * @param string the destination
     */
    public function setDest($value)
    {
        $this->set('dest', (string)$value);
    }
}
