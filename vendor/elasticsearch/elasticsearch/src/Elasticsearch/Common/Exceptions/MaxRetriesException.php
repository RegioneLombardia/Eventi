<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * MaxRetriesException
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class MaxRetriesException extends TransportException implements ElasticsearchException
{
}
