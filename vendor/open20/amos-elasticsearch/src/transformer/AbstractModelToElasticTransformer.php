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

use open20\elasticsearch\base\interfaces\ModelToElasticTransformerInterface;


abstract class AbstractModelToElasticTransformer extends AbstractTransformer implements ModelToElasticTransformerInterface
{
    
    public function transform($model)
    {
        return $model->toArray();
    }

    /**
     * 
     * @param string $value
     * @return string
     */
    protected function filterString($value)
    {
        $retValue = urldecode(html_entity_decode(strip_tags($value)));
        return $retValue;
    }
    
    /**
     * 
     * @param ActiveRecord $model
     * @return boolean
     */
    public function canSaveIndex($model) 
    {
        return true;
    }

}
