<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "event__translation".
*
    * @property integer $event_id
    * @property string $language
    * @property string $title
    * @property string $summary
    * @property string $description
    * @property string $dial_code
    * @property string $conference_call
    * @property string $video_streaming
    * @property string $landing_url
    * @property string $frontend_page_title
    * @property string $frontend_claim
    * @property string $event_location
    * @property string $event_address
    * @property string $event_address_house_number
    * @property string $event_address_cap
    * @property string $gdpr_question_1
    * @property string $gdpr_question_2
    * @property string $gdpr_question_3
    * @property string $gdpr_question_4
    * @property string $gdpr_question_5
    * @property string $thank_you_page_view
    * @property string $token_group_string_code
    * @property string $thank_you_page_already_registered_view
    * @property string $subscribe_form_page_view
    * @property string $email_view
    * @property string $event_closed_page_view
    * @property string $event_full_page_view
    * @property string $ticket_layout_view
    * @property string $email_subscribe_view
    * @property string $email_invitation_custom
    * @property string $email_credential_view
    * @property string $email_ticket_layout_custom
    * @property string $email_ticket_sender
    * @property string $email_ticket_subject
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    *
            * @property \backend\models\translations\EventTranslation $event
    */
class EventTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'event__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['event_id', 'language'], 'required'],
            [['event_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['title', 'summary', 'description', 'dial_code', 'conference_call', 'video_streaming', 'landing_url', 'frontend_page_title', 'frontend_claim', 'event_location', 'event_address', 'event_address_house_number', 'event_address_cap', 'gdpr_question_1', 'gdpr_question_2', 'gdpr_question_3', 'gdpr_question_4', 'gdpr_question_5', 'thank_you_page_view', 'token_group_string_code', 'thank_you_page_already_registered_view', 'subscribe_form_page_view', 'email_view', 'event_closed_page_view', 'event_full_page_view', 'ticket_layout_view', 'email_subscribe_view', 'email_invitation_custom', 'email_credential_view', 'email_ticket_layout_custom', 'email_ticket_sender', 'email_ticket_subject'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\events\models\Event::className(), 'targetAttribute' => ['event_id' => 'id']],
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
    'event_id' => Yii::t('amostranslation', 'Event ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'title' => Yii::t('amostranslation', 'Title'),
    'summary' => Yii::t('amostranslation', 'Summary'),
    'description' => Yii::t('amostranslation', 'Description'),
    'dial_code' => Yii::t('amostranslation', 'Dial Code'),
    'conference_call' => Yii::t('amostranslation', 'Conference Call'),
    'video_streaming' => Yii::t('amostranslation', 'Video Streaming'),
    'landing_url' => Yii::t('amostranslation', 'Landing Url'),
    'frontend_page_title' => Yii::t('amostranslation', 'Frontend Page Title'),
    'frontend_claim' => Yii::t('amostranslation', 'Frontend Claim'),
    'event_location' => Yii::t('amostranslation', 'Event Location'),
    'event_address' => Yii::t('amostranslation', 'Event Address'),
    'event_address_house_number' => Yii::t('amostranslation', 'Event Address House Number'),
    'event_address_cap' => Yii::t('amostranslation', 'Event Address Cap'),
    'gdpr_question_1' => Yii::t('amostranslation', 'Gdpr Question 1'),
    'gdpr_question_2' => Yii::t('amostranslation', 'Gdpr Question 2'),
    'gdpr_question_3' => Yii::t('amostranslation', 'Gdpr Question 3'),
    'gdpr_question_4' => Yii::t('amostranslation', 'Gdpr Question 4'),
    'gdpr_question_5' => Yii::t('amostranslation', 'Gdpr Question 5'),
    'thank_you_page_view' => Yii::t('amostranslation', 'Thank You Page View'),
    'token_group_string_code' => Yii::t('amostranslation', 'Token Group String Code'),
    'thank_you_page_already_registered_view' => Yii::t('amostranslation', 'Thank You Page Already Registered View'),
    'subscribe_form_page_view' => Yii::t('amostranslation', 'Subscribe Form Page View'),
    'email_view' => Yii::t('amostranslation', 'Email View'),
    'event_closed_page_view' => Yii::t('amostranslation', 'Event Closed Page View'),
    'event_full_page_view' => Yii::t('amostranslation', 'Event Full Page View'),
    'ticket_layout_view' => Yii::t('amostranslation', 'Ticket Layout View'),
    'email_subscribe_view' => Yii::t('amostranslation', 'Email Subscribe View'),
    'email_invitation_custom' => Yii::t('amostranslation', 'Email Invitation Custom'),
    'email_credential_view' => Yii::t('amostranslation', 'Email Credential View'),
    'email_ticket_layout_custom' => Yii::t('amostranslation', 'Email Ticket Layout Custom'),
    'email_ticket_sender' => Yii::t('amostranslation', 'Email Ticket Sender'),
    'email_ticket_subject' => Yii::t('amostranslation', 'Email Ticket Subject'),
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
    public function getEvent()
    {
    return $this->hasOne(\open20\amos\events\models\Event::className(), ['id' => 'event_id']);
    }
}
