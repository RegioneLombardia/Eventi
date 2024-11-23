<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * ServerErrorResponseException
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class ServerErrorResponseException extends TransportException implements ElasticsearchException
{
}
