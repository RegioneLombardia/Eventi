<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * InvalidArgumentException
 *
 * Denote invalid or incorrect argument values
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class InvalidArgumentException extends \InvalidArgumentException implements ElasticsearchException
{
}
