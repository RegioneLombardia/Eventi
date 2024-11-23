<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * RequestTimeout408Exception
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class RequestTimeout408Exception extends BadRequest400Exception implements ElasticsearchException
{
}
