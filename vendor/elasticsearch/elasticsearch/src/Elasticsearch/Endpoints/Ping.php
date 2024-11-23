<?php

declare(strict_types = 1);

namespace Elasticsearch\Endpoints;

/**
 * Class Ping
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints
 */
class Ping extends AbstractEndpoint
{
    /**
     * @return string
     */
    public function getURI()
    {
        $uri   = "/";

        return $uri;
    }

    /**
     * @return string[]
     */
    public function getParamWhitelist()
    {
        return array(
        );
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'HEAD';
    }
}
