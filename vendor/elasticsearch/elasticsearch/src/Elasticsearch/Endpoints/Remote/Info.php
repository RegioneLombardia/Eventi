<?php

declare(strict_types = 1);

namespace Elasticsearch\Endpoints\Remote;

use Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class Info
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Cluster\Nodes
 */
class Info extends AbstractEndpoint
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
        return array();
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'GET';
    }
}
