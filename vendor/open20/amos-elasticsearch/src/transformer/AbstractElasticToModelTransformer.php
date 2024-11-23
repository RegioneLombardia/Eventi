<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\base
 * @category   CategoryName
 */
namespace open20\elasticsearch\transformer;

use open20\elasticsearch\base\interfaces\ElasticToModelTransformerInterface;
use RuntimeException;

abstract class AbstractElasticToModelTransformer extends AbstractTransformer implements ElasticToModelTransformerInterface
{
    
    
    /**
     * Transforms an array of elastica objects into an array of
     * model objects fetched from the doctrine repository.
     *
     * @param array $elasticObjects of elastic objects
     *
     * @throws RuntimeException
     *
     * @return BaseOject
     **/
    public function transform($elasticObject)
    {
        $class = $this->getObjectClass();
        
        $model = new $class($elasticObject['_source']);
        $model->id = $elasticObject['_id'];
        return $model;
    }

}
