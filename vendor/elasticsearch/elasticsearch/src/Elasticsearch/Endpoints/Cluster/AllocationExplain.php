<?php

declare(strict_types = 1);

namespace Elasticsearch\Endpoints\Cluster;

use Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class AllocationExplain
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Cluster
 */
class AllocationExplain extends AbstractEndpoint
{

    /**
     * @param array $body
     *
     * @throws \Elasticsearch\Common\Exceptions\InvalidArgumentException
     * @return $this
     */
    public function setBody($body)
    {
        if (isset($body) !== true) {
            return $this;
        }

        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getURI()
    {
        return "/_cluster/allocation/explain";
    }

    /**
     * @return string[]
     */
    public function getParamWhitelist()
    {
        return array(
            'include_yes_decisions',
            'include_disk_info',
        );
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return isset($this->body) ? 'POST' : 'GET';
    }
}
