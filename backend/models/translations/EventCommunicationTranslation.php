<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "event_communication__translation".
*
    * @property integer $event_communication_id
    * @property string $language
    * @property string $title
    * @property string $text_email
    * @property string $subject_email
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    *
            * @property \backend\models\translations\EventCommunicationTranslation $eventCommunication
    */
class EventCommunicationTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'event_communication__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['event_communication_id', 'language'], 'required'],
            [['event_communication_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['title', 'text_email', 'subject_email'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['event_communication_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\events\models\EventCommunication::className(), 'targetAttribute' => ['event_communication_id' => 'id']],
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
    'event_communication_id' => Yii::t('amostranslation', 'Event Communication ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'title' => Yii::t('amostranslation', 'Title'),
    'text_email' => Yii::t('amostranslation', 'Text Email'),
    'subject_email' => Yii::t('amostranslation', 'Subject Email'),
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
    public function getEventCommunication()
    {
    return $this->hasOne(\open20\amos\events\models\EventCommunication::className(), ['id' => 'event_communication_id']);
    }
}
