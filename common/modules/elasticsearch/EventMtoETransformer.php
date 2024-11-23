<?php
namespace common\modules\elasticsearch;

use open20\amos\events\models\Event;
use open20\elasticsearch\transformer\modeltransformers\BaseModelToElasticTransformer;

class EventMtoETransformer extends BaseModelToElasticTransformer 
{

    /* @var $model Event */
    public function transform($model) 
    {
        $landing = $model->eventLanding;
        $location = $model->eventLocation;
        $locationEntrance = $model->eventLocationEntrance;

        $locationString  ='';
        $locationEntranceString = '';
        if($location){
            $locationString .= $location->name;
        }
        if($locationEntrance){
            $locationEntranceString .= ' '. $locationEntrance->name;
        }

        $values = [];
        $values['title'] = $this->filterString($model->title);
        $values['description'] = $this->filterString($landing->description);
        $values['schedule'] = $this->filterString($landing->schedule);
        $values['begin_date_hour'] =  date("c",strtotime($model->begin_date_hour));
        $values['location'] = $this->filterString($locationString);
        $values['location_entrance'] = $this->filterString($locationEntranceString);
        //Controllare se pubblico
        $values['public'] = $model->isEventTypePublic() ? '1' : '0';
//        $values['tags'] = $this->getTags($model);
        $values['start_publication'] =  date("c",strtotime($model->publication_date_begin));
        $values['end_publication'] =  date("c",strtotime($model->publication_date_end));
        
        return $values;
    }


    public function canSaveIndex($model)
    {
        $ret = false;
        if($model->status == Event::EVENTS_WORKFLOW_STATUS_PUBLISHED){
            $ret = true;
        }
        return $ret;
    }
}
