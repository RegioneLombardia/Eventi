<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * Unauthorized401Exception, thrown on 401 unauthorized http error
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class Unauthorized401Exception extends \Exception implements ElasticsearchException
{
}
