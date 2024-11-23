<?php

declare(strict_types = 1);

namespace Elasticsearch\Endpoints;

use Elasticsearch\Serializers\SerializerInterface;
use Elasticsearch\Transport;

/**
 * Interface BulkEndpointInterface
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints
 */
interface BulkEndpointInterface
{
    /**
     * Constructor
     *
     * @param SerializerInterface $serializer A serializer
     */
    public function __construct(SerializerInterface $serializer);
}
