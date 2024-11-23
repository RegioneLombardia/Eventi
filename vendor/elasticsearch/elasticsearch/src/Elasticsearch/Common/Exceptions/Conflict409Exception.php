<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * Conflict409Exception, thrown on 409 conflict http error
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class Conflict409Exception extends \Exception implements ElasticsearchException
{
}
