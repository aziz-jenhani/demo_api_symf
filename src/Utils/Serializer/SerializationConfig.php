<?php

namespace App\Utils\Serializer;

/**
 * The model for the configuration of serialization strategies.
 *
 * @author Fondative <devteam@fondative.com>
 */
class  SerializationConfig
{
    /**
     * @var array
     */
    private $contextGroups;

    /**
     * @var bool
     */
    private $enableMaxDepthCheck;

    /**
     * @var bool
     */
    private $serializeNull = true;

    /**
     * @return array
     */
    public function getContextGroups()
    {
        return $this->contextGroups;
    }

    /**
     * @param array $contextGroups
     * @return SerializationConfig
     */
    public function setContextGroups(array $contextGroups)
    {
        $this->contextGroups = $contextGroups;
        return $this;
    }

    /**
     * @return bool
     */
    public function shouldEnableMaxDepthCheck()
    {
        return $this->enableMaxDepthCheck;
    }

    /**
     * @param bool $enableMaxDepthCheck
     * @return SerializationConfig
     */
    public function setEnableMaxDepthCheck(bool $enableMaxDepthCheck)
    {
        $this->enableMaxDepthCheck = $enableMaxDepthCheck;
        return $this;
    }

    /**
     * @return bool
     */
    public function shouldSerializeNull()
    {
        return $this->serializeNull;
    }
}