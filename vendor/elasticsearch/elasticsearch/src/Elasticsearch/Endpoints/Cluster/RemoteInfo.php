<?php

declare(strict_types = 1);

namespace Elasticsearch\Endpoints\Cluster;

use Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * RemoteInfo Health
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Cluster
 */
class RemoteInfo extends AbstractEndpoint
{
    /**
     * @return string
     */
    public function getURI()
    {
        return "/_remote/info";
    }

    /**
     * @return string[]
     */
    public function getParamWhitelist()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'GET';
    }
}
