<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * Class ClientErrorResponseException
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class ClientErrorResponseException extends TransportException implements ElasticsearchException
{
}
