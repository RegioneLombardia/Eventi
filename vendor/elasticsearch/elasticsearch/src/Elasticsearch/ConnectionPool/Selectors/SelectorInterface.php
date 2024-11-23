<?php

declare(strict_types = 1);

namespace Elasticsearch\ConnectionPool\Selectors;

/**
 * Class RandomSelector
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Connections\Selectors
 */
interface SelectorInterface
{
    /**
     * Perform logic to select a single ConnectionInterface instance from the array provided
     *
     * @param \Elasticsearch\Connections\ConnectionInterface[] $connections an array of ConnectionInterface instances to choose from
     *
     * @return \Elasticsearch\Connections\ConnectionInterface
     */
    public function select($connections);
}
