<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * RoutingMissingException, thrown on when a routing value is required but
 * not provided
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class RoutingMissingException extends ServerErrorResponseException implements ElasticsearchException
{
}
