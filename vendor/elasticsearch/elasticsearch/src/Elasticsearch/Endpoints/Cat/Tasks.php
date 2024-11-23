<?php

declare(strict_types = 1);

namespace Elasticsearch\Endpoints\Cat;

use Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class Tasks
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Cat
 */
class Tasks extends AbstractEndpoint
{
    /**
     * @return string
     */
    public function getURI()
    {
        return "/_cat/tasks";
    }

    /**
     * @return string[]
     */
    public function getParamWhitelist()
    {
        return array(
            'format',
            'node_id',
            'actions',
            'detailed',
            'parent_node',
            'parent_task',
            'h',
            'help',
            'v',
            's'
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
