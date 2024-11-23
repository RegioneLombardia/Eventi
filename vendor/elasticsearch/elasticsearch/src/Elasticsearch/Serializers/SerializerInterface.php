<?php

declare(strict_types = 1);

namespace Elasticsearch\Serializers;

/**
 * Interface SerializerInterface
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Serializers
 */
interface SerializerInterface
{
    /**
     * Serialize a complex data-structure into a json encoded string
     *
     * @param mixed $data The data to encode
     *
     * @return string
     */
    public function serialize($data);

    /**
     * Deserialize json encoded string into an associative array
     *
     * @param string $data    JSON encoded string
     * @param array  $headers Response Headers
     *
     * @return array
     */
    public function deserialize($data, $headers);
}
