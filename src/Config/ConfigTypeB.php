<?php

declare(strict_types=1);

namespace phpsap\classes\Config;

use phpsap\classes\Config\Traits\CommonTrait;
use phpsap\classes\Config\Traits\JsonDecodeTrait;
use phpsap\classes\Util\JsonSerializable;
use phpsap\exceptions\IncompleteConfigException;
use phpsap\exceptions\InvalidArgumentException;
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
final class ConfigTypeB extends JsonSerializable implements IConfigTypeB
{
    use CommonTrait;
    use JsonDecodeTrait;

    /**
     * Get an array of all valid keys this class is able to set().
     * @return array<int, string>
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getAllowedKeys(): array
    {
        return array_merge(
            $this->getCommonAllowedKeys(),
            [
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
            ]
        );
    }

    /**
     * Get the host name of the message server.
     * @return string
     * @throws IncompleteConfigException
     * @throws InvalidArgumentException
     */
    public function getMshost(): string
    {
        return $this->get(self::JSON_MSHOST);
    }

    /**
     * Set the host name of the message server.
     * @param string $mshost The host name of the message server.
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setMshost(string $mshost): IConfigTypeB
    {
        $this->set(self::JSON_MSHOST, $mshost);
        return $this;
    }

    /**
     * Get the name of SAP system, optional; default: destination
     * @return string|null The name of the SAP system or NULL in case no name has been defined.
     * @throws InvalidArgumentException
     */
    public function getR3name(): ?string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_R3NAME);
    }

    /**
     * Set the name of SAP system, optional; default: destination
     * @param string $r3name The name of the SAP system.
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setR3name(string $r3name): IConfigTypeB
    {
        $this->set(self::JSON_R3NAME, $r3name);
        return $this;
    }

    /**
     * Get the group name of the application servers, optional; default: PUBLIC.
     * @return string|null group name of the application servers
     * @throws InvalidArgumentException
     */
    public function getGroup(): ?string
    {
        /**
         * InvalidArgumentException will never be thrown.
         */
        return $this->get(self::JSON_GROUP);
    }

    /**
     * Set the group name of the application servers, optional; default: PUBLIC.
     * @param string $group The group name of the application servers.
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setGroup(string $group): IConfigTypeB
    {
        $this->set(self::JSON_GROUP, $group);
        return $this;
    }
}
