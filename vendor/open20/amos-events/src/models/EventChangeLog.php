<?php

namespace open20\amos\events\models;

use open20\amos\core\models\ModelsClassname;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "event_change_log".
 */
class EventChangeLog extends \open20\amos\events\models\base\EventChangeLog
{
    public function representingColumn()
    {
        return [
//inserire il campo o i campi rappresentativi del modulo
        ];
    }

    public function attributeHints()
    {
        return [
        ];
    }

    /**
     * Returns the text hint for the specified attribute.
     * @param string $attribute the attribute name
     * @return string the attribute hint
     */
    public function getAttributeHint($attribute)
    {
        $hints = $this->attributeHints();
        return isset($hints[$attribute]) ? $hints[$attribute] : null;
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
        ]);
    }

    public function attributeLabels()
    {
        return
            ArrayHelper::merge(
                parent::attributeLabels(),
                [
                ]);
    }


    public static function getEditFields()
    {
        $labels = self::attributeLabels();

        return [
            [
                'slug' => 'event_id',
                'label' => $labels['event_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'user_id',
                'label' => $labels['user_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'update_models_classname_id',
                'label' => $labels['update_models_classname_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'update_record_id',
                'label' => $labels['update_record_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'type_operation',
                'label' => $labels['type_operation'],
                'type' => 'string'
            ],
            [
                'slug' => 'name',
                'label' => $labels['name'],
                'type' => 'string'
            ],
            [
                'slug' => 'description',
                'label' => $labels['description'],
                'type' => 'text'
            ],
        ];
    }

    /**
     * @return string marker path
     */
    public function getIconMarker()
    {
        return null; //TODO
    }

    /**
     * If events are more than one, set 'array' => true in the calendarView in the index.
     * @return array events
     */
    public function getEvents()
    {
        return NULL; //TODO
    }

    /**
     * @return url event (calendar of activities)
     */
    public function getUrlEvent()
    {
        return NULL; //TODO e.g. Yii::$app->urlManager->createUrl([]);
    }

    /**
     * @return color event
     */
    public function getColorEvent()
    {
        return NULL; //TODO
    }

    /**
     * @return title event
     */
    public function getTitleEvent()
    {
        return NULL; //TODO
    }



    /**
     * @param $event_id
     * @param $model
     * @param $name
     * @param string $type
     */
    public static function saveChangeLog($event_id, $model, $name, $type = 'update_event')
    {
        $log = new EventChangeLog();
        $log->event_id = $event_id;
        $log->user_id = \Yii::$app->user->id;
        $model_classname = ModelsClassname::find()->andWhere(['classname' => get_class($model)])->one();
        if ($model_classname) {
            $model_classname_id = $model_classname->id;
            $log->update_models_classname_id = $model_classname_id;
        }
        $log->update_record_id = $model->id;
        $log->type_operation = $type;
        $log->name = $name;
        $log->save(false);
    }

    /**
     * @param $event_id
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function getLastUpdatedBy($event_id)
    {
        $log = EventChangeLog::find()
            ->andWhere(['event_id' => $event_id])
            ->orderBy('id DESC')
            ->one();
        return $log;
    }


}
