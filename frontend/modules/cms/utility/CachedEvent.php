<?php

namespace app\modules\cms\utility;

class CachedEvent
{
    public static $modelEvent = null;
    public static $modelLanding = null;

    /**
     * @param $params
     * @return \open20\amos\events\models\Event|null
     */
    public static function getModelEvent($params = []){
        if(!empty(self::$modelEvent)){
            return self::$modelEvent;
        }
        if(!empty($params)){
            $model = \backend\modules\eventsadmin\utility\EventsAdminUtility::getModelEventFormLuya($params);
            if(!empty($model)){
                self::$modelEvent = $model;
            }
        }
        return self::$modelEvent;
    }

    /**
     * @return null
     */
    public static function getModelLanding(){
        if(!empty(self::$modelLanding)){
            return self::$modelLanding;
        }
        if(!empty(self::$modelEvent)){
            self::$modelLanding = self::$modelEvent->eventLanding;
        }

        return self::$modelLanding;
    }


}