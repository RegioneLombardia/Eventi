<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\base
 * @category   CategoryName
 */
namespace open20\elasticsearch\transformer\modeltransformers;

use open20\elasticsearch\transformer\AbstractModelToElasticTransformer;

class BaseModelToElasticTransformer extends AbstractModelToElasticTransformer
{
    
    const MIN_DATE = '0000-00-00 00:00:00';
    const MAX_DATE = '9999-12-31 23:59:59';
    
    public $tagValuesSeparatorAttribute = "','";
    
    public function getTags($model)
    {
        $string_tags = '';
        
        $tags = $model->getTagValues(true); 
        $string_tags = sprintf("'%s'", implode($this->tagValuesSeparatorAttribute, $tags));
        return $string_tags;
    }
}
