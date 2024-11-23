<?php

declare(strict_types = 1);

namespace Elasticsearch\Endpoints\Cat;

use Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class Allocation
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Cat
 */
class Allocation extends AbstractEndpoint
{
    /**
     * A comma-separated list of node IDs or names to limit the returned information
     *
     * @var string
     */
    private $node_id;

    /**
     * @param string $node_id
     *
     * @return $this
     */
    public function setNodeId($node_id)
    {
        if (isset($node_id) !== true) {
            return $this;
        }

        $this->node_id = $node_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getURI()
    {
        $node_id = $this->node_id;
        $uri   = "/_cat/allocation";

        if (isset($node_id) === true) {
            $uri = "/_cat/allocation/$node_id";
        }

        return $uri;
    }

    /**
     * @return string[]
     */
    public function getParamWhitelist()
    {
        return array(
            'bytes',
            'local',
            'master_timeout',
            'h',
            'help',
            'v',
            's',
            'format',
        );
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'GET';
    }
}
