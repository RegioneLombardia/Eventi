<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "event_landing__translation".
*
    * @property integer $event_landing_id
    * @property string $language
    * @property string $description
    * @property string $schedule
    * @property string $contact_info_organizator
    * @property string $thank_you_waiting_list
    * @property string $thank_you_registered
    * @property string $thank_you_subscribe
    * @property string $url
    * @property string $streaming_url
    * @property string $subject_sending_memo
    * @property string $text_sending_memo
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    * @property string $title_request_info
    * @property string $title_documents
    * @property string $title_info
    * @property string $title_video
    * @property string $title_pics
    * @property string $title_protagonists
    * @property string $gallery_type
    * @property string $title_instagram_video
    * @property string $title_related_events
    * @property string $title_maps
    * @property string $title_presentation
    * @property string $title_related_event
    * @property string $title_schedule
    * @property string $description_protagonists
    * @property string $rating_title
    * @property string $rating_description
    *
            * @property \backend\models\translations\EventLandingTranslation $eventLanding
    */
class EventLandingTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'event_landing__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['event_landing_id', 'language'], 'required'],
            [['event_landing_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['description', 'schedule', 'contact_info_organizator', 'thank_you_waiting_list', 'thank_you_registered', 'thank_you_subscribe', 'url', 'streaming_url', 'subject_sending_memo', 'text_sending_memo', 'title_request_info', 'title_documents', 'title_info', 'title_video', 'title_pics', 'title_protagonists', 'gallery_type', 'title_instagram_video', 'title_related_events', 'title_maps', 'title_presentation', 'title_related_event', 'title_schedule', 'description_protagonists', 'rating_title', 'rating_description'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['event_landing_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\events\models\EventLanding::className(), 'targetAttribute' => ['event_landing_id' => 'id']],
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
    'event_landing_id' => Yii::t('amostranslation', 'Event Landing ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'description' => Yii::t('amostranslation', 'Description'),
    'schedule' => Yii::t('amostranslation', 'Schedule'),
    'contact_info_organizator' => Yii::t('amostranslation', 'Contact Info Organizator'),
    'thank_you_waiting_list' => Yii::t('amostranslation', 'Thank You Waiting List'),
    'thank_you_registered' => Yii::t('amostranslation', 'Thank You Registered'),
    'thank_you_subscribe' => Yii::t('amostranslation', 'Thank You Subscribe'),
    'url' => Yii::t('amostranslation', 'Url'),
    'streaming_url' => Yii::t('amostranslation', 'Streaming Url'),
    'subject_sending_memo' => Yii::t('amostranslation', 'Subject Sending Memo'),
    'text_sending_memo' => Yii::t('amostranslation', 'Text Sending Memo'),
    'status' => Yii::t('amostranslation', 'Status'),
    'created_by' => Yii::t('amostranslation', 'Created By'),
    'updated_by' => Yii::t('amostranslation', 'Updated By'),
    'deleted_by' => Yii::t('amostranslation', 'Deleted By'),
    'created_at' => Yii::t('amostranslation', 'Created At'),
    'updated_at' => Yii::t('amostranslation', 'Updated At'),
    'deleted_at' => Yii::t('amostranslation', 'Deleted At'),
    'title_request_info' => Yii::t('amostranslation', 'Title Request Info'),
    'title_documents' => Yii::t('amostranslation', 'Title Documents'),
    'title_info' => Yii::t('amostranslation', 'Title Info'),
    'title_video' => Yii::t('amostranslation', 'Title Video'),
    'title_pics' => Yii::t('amostranslation', 'Title Pics'),
    'title_protagonists' => Yii::t('amostranslation', 'Title Protagonists'),
    'gallery_type' => Yii::t('amostranslation', 'Gallery Type'),
    'title_instagram_video' => Yii::t('amostranslation', 'Title Instagram Video'),
    'title_related_events' => Yii::t('amostranslation', 'Title Related Events'),
    'title_maps' => Yii::t('amostranslation', 'Title Maps'),
    'title_presentation' => Yii::t('amostranslation', 'Title Presentation'),
    'title_related_event' => Yii::t('amostranslation', 'Title Related Event'),
    'title_schedule' => Yii::t('amostranslation', 'Title Schedule'),
    'description_protagonists' => Yii::t('amostranslation', 'Description Protagonists'),
    'rating_title' => Yii::t('amostranslation', 'Rating Title'),
    'rating_description' => Yii::t('amostranslation', 'Rating Description'),
];
}

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getEventLanding()
    {
    return $this->hasOne(\open20\amos\events\models\EventLanding::className(), ['id' => 'event_landing_id']);
    }
}
