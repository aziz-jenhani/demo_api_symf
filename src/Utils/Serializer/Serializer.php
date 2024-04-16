<?php

namespace App\Utils\Serializer;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

/**
 * Custom implementation for the serializer.
 *
 * @author Fondative <devteam@fondative.com>
 */
class Serializer
{
    /**
     * The serializer property
     *
     * @var \JMS\Serializer\Serializer
     */
    private $serializer;

    /**
     * Serializer constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Serialize objects default format (json)
     *
     * @param $data
     * @param string $format
     * @param SerializationConfig $config
     * @return mixed|string
     */
    public function serialize($data, $format = 'json', SerializationConfig $config = null)
    {
        return $this->serializer->serialize($data, $format, $this->createSerializationContext($config));
    }

    /**
     * @param SerializationConfig $config
     * @return SerializationContext
     */
    private function createSerializationContext(SerializationConfig $config)
    {
        $context = new SerializationContext();

        $context
            ->setGroups($config->getContextGroups() ?: ['Default'])
            ->enableMaxDepthChecks()
            ->setSerializeNull($config->shouldSerializeNull());

        if ($config->shouldEnableMaxDepthCheck()) {
            $context->enableMaxDepthChecks();
        }

        return $context;
    }

    /**
     * Converts objects to an array structure.
     *
     * @param mixed $data anything that converts to an array, typically an object or an array of objects
     * @param SerializationConfig $config
     * @return array
     */
    public function normalize($data, SerializationConfig $config = null)
    {
        return $this->serializer->toArray($data, $this->createSerializationContext($config));
    }

    /**
     * Create object from an array
     *
     * @param array $data
     * @param $type
     * @param DeserializationContext|null $context
     * @return mixed
     */
    public function denormalize(array $data, $type, DeserializationContext $context = null)
    {
        return $this->serializer->fromArray($data, $type, $context);
    }
}