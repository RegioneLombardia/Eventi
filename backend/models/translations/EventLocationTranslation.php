<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "event_location__translation".
*
    * @property integer $event_location_id
    * @property string $language
    * @property string $name
    * @property string $description
    * @property string $event_place_id
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    *
            * @property \backend\models\translations\EventLocationTranslation $eventLocation
    */
class EventLocationTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'event_location__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['event_location_id', 'language'], 'required'],
            [['event_location_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['name', 'description', 'event_place_id'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['event_location_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\events\models\EventLocation::className(), 'targetAttribute' => ['event_location_id' => 'id']],
 ['language_source', 'safe'],
];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
'language_source' => \Yii::t('amostranslation', 'Select another source language'),
    'event_location_id' => Yii::t('amostranslation', 'Event Location ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'name' => Yii::t('amostranslation', 'Name'),
    'description' => Yii::t('amostranslation', 'Description'),
    'event_place_id' => Yii::t('amostranslation', 'Event Place ID'),
    'status' => Yii::t('amostranslation', 'Status'),
    'created_by' => Yii::t('amostranslation', 'Created By'),
    'updated_by' => Yii::t('amostranslation', 'Updated By'),
    'deleted_by' => Yii::t('amostranslation', 'Deleted By'),
    'created_at' => Yii::t('amostranslation', 'Created At'),
    'updated_at' => Yii::t('amostranslation', 'Updated At'),
    'deleted_at' => Yii::t('amostranslation', 'Deleted At'),
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getEventLocation()
    {
    return $this->hasOne(\open20\amos\events\models\EventLocation::className(), ['id' => 'event_location_id']);
    }
}
