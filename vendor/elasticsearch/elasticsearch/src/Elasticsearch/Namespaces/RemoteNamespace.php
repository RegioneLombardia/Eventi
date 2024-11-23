<?php

declare(strict_types = 1);

namespace Elasticsearch\Namespaces;

use Elasticsearch\Endpoints\Remote\Info;

/**
 * Class RemoteNamespace
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Namespaces\TasksNamespace
 */
class RemoteNamespace extends AbstractNamespace
{
    /**
     * @param array $params Associative array of parameters
     *
     * @return array
     */
    public function info($params = array())
    {
        /** @var callable $endpointBuilder */
        $endpointBuilder = $this->endpoints;

        /** @var Info $endpoint */
        $endpoint = $endpointBuilder('Remote\Info');
        $endpoint->setParams($params);

        return $this->performRequest($endpoint);
    }
}
