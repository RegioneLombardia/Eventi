<?php

namespace open20\amos\groups\models;

use open20\amos\groups\Module;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "groups_comunication".
 */
class GroupsComunication extends \open20\amos\groups\models\base\GroupsComunication
{
    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;

    public $email;

    public function getStatusLabel(){
        if($this->status == self::STATUS_DRAFT){
            return Module::t('amosgroups', "Bozza");
        }elseif ($this->status == self::STATUS_SENT){
            return Module::t('amosgroups', "Inviata");
        }
        return '';
    }

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
                'slug' => 'groups_id',
                'label' => $labels['groups_id'],
                'type' => 'integer'
            ],
            [
                'slug' => 'subject',
                'label' => $labels['subject'],
                'type' => 'text'
            ],
            [
                'slug' => 'text',
                'label' => $labels['text'],
                'type' => 'text'
            ],
            [
                'slug' => 'layout_path',
                'label' => $labels['layout_path'],
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
