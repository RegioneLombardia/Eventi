<?php

declare(strict_types = 1);

namespace Elasticsearch\Endpoints\Indices\Template;

use Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class AbstractTemplateEndpoint
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Endpoints\Indices\Template
 */
abstract class AbstractTemplateEndpoint extends AbstractEndpoint
{
    /** @var  string */
    protected $name;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
