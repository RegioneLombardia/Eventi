<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\admin\base
 * @category   CategoryName
 */
namespace open20\elasticsearch\base\interfaces;


interface ModelToElasticTransformerInterface 
{
    /**
     * Returns the object class used by the transformer.
     */
    public function getObjectClass();
    
    public function transform($model);
    
    public function canSaveIndex($model);
}
