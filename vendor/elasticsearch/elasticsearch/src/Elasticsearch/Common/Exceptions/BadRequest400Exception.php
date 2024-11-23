<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * BadRequest400Exception, thrown on 400 conflict http error
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class BadRequest400Exception extends \Exception implements ElasticsearchException
{
}
