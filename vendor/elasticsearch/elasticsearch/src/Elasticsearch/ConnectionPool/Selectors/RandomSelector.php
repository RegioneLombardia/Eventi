<?php

declare(strict_types = 1);

namespace Elasticsearch\ConnectionPool\Selectors;

use Elasticsearch\Connections\ConnectionInterface;

/**
 * Class RandomSelector
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Connections\Selectors\RandomSelector
 */
class RandomSelector implements SelectorInterface
{
    /**
     * Select a random connection from the provided array
     *
     * @param  ConnectionInterface[] $connections an array of ConnectionInterface instances to choose from
     *
     * @return \Elasticsearch\Connections\ConnectionInterface
     */
    public function select($connections)
    {
        return $connections[array_rand($connections)];
    }
}
