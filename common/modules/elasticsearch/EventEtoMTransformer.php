<?php

namespace common\modules\elasticsearch;

use open20\elasticsearch\transformer\modeltransformers\BaseElasticToModelTRansformer;

class EventEtoMTransformer extends BaseElasticToModelTRansformer
{
    /* @var $model ElasticIndex */

    public function transform($elasticObject)
    {
        $values                = $elasticObject['_source'];
        $values['primo_piano'] = $values['public'];
        unset($values['public']);
        unset($values['schedule']);
//        unset($values['tags']);
        unset($values['location']);
        unset($values['location_entrance']);
        unset($values['start_publication']);
        unset($values['end_publication']);
        unset($values['subtitle']);

        $class     = $this->getObjectClass();
        $model     = new $class($values);
        $model->id = $elasticObject['_id'];
        return $model;
    }
}