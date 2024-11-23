<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * BadMethodCallException
 *
 * Denote problems with a method call (e.g. incorrect number of arguments)
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class BadMethodCallException extends \BadMethodCallException implements ElasticsearchException
{
}
