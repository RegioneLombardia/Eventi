<?php
declare(strict_types = 1);

namespace Elasticsearch\Endpoints;

/**
 * Class RankEval
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints
 */
class RankEval extends AbstractEndpoint
{
    /**
     * @return array
     */
    public function getParamWhitelist()
    {
        return [
            'ignore_unavailable',
            'allow_no_indices',
            'expand_wildcards'
        ];
    }

    /**
     * @return string
     */
    public function getURI()
    {
        $index = $this->index ?? null;
        if (isset($index)) {
            return "/$index/_rank_eval";
        }
        return '/_rank_eval';
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'POST';
    }

    /**
     * @param array $body
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
}
