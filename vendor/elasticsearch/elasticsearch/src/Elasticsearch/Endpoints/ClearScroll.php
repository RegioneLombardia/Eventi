<?php

declare(strict_types = 1);

namespace Elasticsearch\Endpoints;

use Elasticsearch\Common\Exceptions;

/**
 * Class Clearscroll
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints
 */
class ClearScroll extends AbstractEndpoint
{
    /**
     * A comma-separated list of scroll IDs to clear
     *
     * @var string
     */
    private $scrollId;

    /**
     * @param string $scrollId
     *
     * @return $this
     */
    public function setScrollId($scrollId)
    {
        if (isset($scrollId) !== true) {
            return $this;
        }

        $this->scrollId = $scrollId;

        return $this;
    }

    /**
     * @throws \Elasticsearch\Common\Exceptions\RuntimeException
     * @return string
     */
    public function getURI()
    {
        return "/_search/scroll/";
    }

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
     * @return array
     */
    public function getBody()
    {
        if (isset($this->body)) {
            return $this->body;
        }
        if (is_array($this->scrollId)) {
            return ['scroll_id' => $this->scrollId];
        }
        return ['scroll_id' => [$this->scrollId]];
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
        return 'DELETE';
    }
}
