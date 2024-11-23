<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions\Curl;

use Elasticsearch\Common\Exceptions\ElasticsearchException;
use Elasticsearch\Common\Exceptions\TransportException;

/**
 * Class OperationTimeoutException
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions\Curl
  */
class OperationTimeoutException extends TransportException implements ElasticsearchException
{
}
