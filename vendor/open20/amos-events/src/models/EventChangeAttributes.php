<?php

namespace open20\amos\events\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "event_change_attributes".
 */
class EventChangeAttributes extends \open20\amos\events\models\base\EventChangeAttributes
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
                'slug' => 'user_id',
                'label' => $labels['user_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'event_id',
                'label' => $labels['event_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'operation_type',
                'label' => $labels['operation_type'],
                'type' => 'integer'
            ],
            [
                'slug' => 'operation_group',
                'label' => $labels['operation_group'],
                'type' => 'integer'
            ],
            [
                'slug' => 'model_classname',
                'label' => $labels['model_classname'],
                'type' => 'string'
            ],
            [
                'slug' => 'model_id',
                'label' => $labels['model_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'model_attribute',
                'label' => $labels['model_attribute'],
                'type' => 'string'
            ],
            [
                'slug' => 'old_value',
                'label' => $labels['old_value'],
                'type' => 'string'
            ],
            [
                'slug' => 'new_value',
                'label' => $labels['new_value'],
                'type' => 'string'
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


}
