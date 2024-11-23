<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * ScriptLangNotSupportedException
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class ScriptLangNotSupportedException extends BadRequest400Exception implements ElasticsearchException
{
}
