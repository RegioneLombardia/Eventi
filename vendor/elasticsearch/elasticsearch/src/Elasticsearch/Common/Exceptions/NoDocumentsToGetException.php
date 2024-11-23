<?php

declare(strict_types = 1);

namespace Elasticsearch\Common\Exceptions;

/**
 * NoDocumentsToGetException
 *
 * @category Elasticsearch
 * @package  Elasticsearch\Common\Exceptions
 */
class NoDocumentsToGetException extends ServerErrorResponseException implements ElasticsearchException
{
}
