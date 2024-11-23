<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * UnexpectedValueException
 *
 * Denote a value that is outside the normally accepted values
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class UnexpectedValueException extends \UnexpectedValueException implements ElasticsearchException
{
}
