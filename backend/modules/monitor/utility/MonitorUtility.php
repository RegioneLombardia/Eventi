<?php

namespace backend\modules\monitor\utility;

class MonitorUtility
{
    /**
     * @param $model
     * @return string
     */
    public static function getDescriptionHistory($model){
        $modelsClassname = \open20\amos\core\models\ModelsClassname::findOne($model->models_classname_id);
        if ($modelsClassname) {
            $class = $modelsClassname->classname;
            $object = $class::findOne($model->record_id);
        }
        if ($model->type == \open20\amos\events\models\Event::LOG_TYPE_INVITATION_SENT) {
            $event = $object->eventInternalList->event;
            $a = "Evento - " . \yii\helpers\Html::a($event->title, ['/events/event-dashboard/view', 'id' => $event->id]) . "<br>"
                . "Dg - " . $event->eventGroupReferent->denominazione;
            return $a;
//
        }
        elseif ($model->type == \open20\amos\events\models\Event::LOG_TYPE_COMMUNICATION_SENT) {
            $event = $object->eventCommunication->event;
            $a = "Titolo - ".$object->eventCommunication->title."<br>".
                "Evento - " . \yii\helpers\Html::a($event->title, ['/events/event-dashboard/view', 'id' => $event->id]) . "<br>"
                . "Dg - " . $event->eventGroupReferent->denominazione;
            return $a;
        }
        elseif ($model->type == \open20\amos\events\models\Event::LOG_TYPE_SUBSCRIBE_EVENT) {
            $a = "Evento - " . \yii\helpers\Html::a($object->title, ['/events/event-dashboard/view', 'id' => $object->id]) . "<br>"
                . "Dg - " . $object->eventGroupReferent->denominazione;
            return $a;
        } elseif ($model->type == \open20\amos\events\models\Event::LOG_TYPE_UNSUBSCRIBE_EVENT) {
            $a = "Evento - " . \yii\helpers\Html::a($object->title, ['/events/event-dashboard/view', 'id' => $object->id]) . "<br>"
                . "Dg - " . $object->eventGroupReferent->denominazione;
            return $a;
        } elseif ($model->type == \open20\amos\events\models\Event::LOG_TYPE_ATTENDANT_TO_EVENT) {
            if ($object instanceof \open20\amos\events\models\EventParticipantCompanion) {
                $event = $object->eventInvitation->event;
                $a = "Evento - " . \yii\helpers\Html::a($event->title, ['/events/event-dashboard/view', 'id' => $event->id]) . "<br>"
                    . "Dg - " . $event->eventGroupReferent->denominazione;
                return $a;
            } else {
                $event = $object->event;
                $a = "Evento - " . \yii\helpers\Html::a($event->title, ['/events/event-dashboard/view', 'id' => $event->id]) . "<br>"
                    . "Dg - " . $event->eventGroupReferent->denominazione;
                return $a;
            }
        } elseif ($model->type == \open20\amos\admin\models\UserProfile::LOG_TYPE_UPDATE_PROFILE) {
            $str = '';
            $attrChangeLogs = $model->attributesChangeLogs;
            foreach ($attrChangeLogs as $log) {
                $str .= "<strong>" . $log->model_attribute . '</strong>: ' . $log->old_value . ' => ' . $log->new_value . "<br>";
            }
            return $str;
        } else {
            return '';
        }
    }

}