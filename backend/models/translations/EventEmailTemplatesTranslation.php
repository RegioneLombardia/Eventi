<?php

namespace backend\models\translations;

use Yii;
use raoul2000\workflow\base\SimpleWorkflowBehavior;
use yii\helpers\ArrayHelper;

/**
* This is the base-model class for table "event_email_templates__translation".
*
    * @property integer $event_email_templates_id
    * @property string $language
    * @property string $save_the_date
    * @property string $save_the_date_subject
    * @property string $registration_email
    * @property string $registration_email_subject
    * @property string $confirm_registration
    * @property string $confirm_registration_subject
    * @property string $info_waiting_list
    * @property string $info_waiting_list_subject
    * @property string $webmeeting_webex
    * @property string $webmeeting_webex_subject
    * @property string $webmeeting_webex_confirm_reg
    * @property string $webmeeting_webex_confirm_reg_subject
    * @property string $webmeeting_webex_save_date
    * @property string $webmeeting_webex_save_date_subject
    * @property string $send_tickets
    * @property string $send_tickets_subject
    * @property string $status
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    *
            * @property \backend\models\translations\EventEmailTemplatesTranslation $eventEmailTemplates
    */
class EventEmailTemplatesTranslation extends \open20\amos\core\record\Record
{


    public $language_source;


/**
* @inheritdoc
*/
public static function tableName()
{
return 'event_email_templates__translation';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['event_email_templates_id', 'language'], 'required'],
            [['event_email_templates_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['save_the_date', 'save_the_date_subject', 'registration_email', 'registration_email_subject', 'confirm_registration', 'confirm_registration_subject', 'info_waiting_list', 'info_waiting_list_subject', 'webmeeting_webex', 'webmeeting_webex_subject', 'webmeeting_webex_confirm_reg', 'webmeeting_webex_confirm_reg_subject', 'webmeeting_webex_save_date', 'webmeeting_webex_save_date_subject', 'send_tickets', 'send_tickets_subject'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['language', 'status'], 'string', 'max' => 255],
            [['event_email_templates_id'], 'exist', 'skipOnError' => true, 'targetClass' => \open20\amos\events\models\EventEmailTemplates::className(), 'targetAttribute' => ['event_email_templates_id' => 'id']],
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
    'event_email_templates_id' => Yii::t('amostranslation', 'Event Email Templates ID'),
    'language' => Yii::t('amostranslation', 'Language'),
    'save_the_date' => Yii::t('amostranslation', 'Save The Date'),
    'save_the_date_subject' => Yii::t('amostranslation', 'Save The Date Subject'),
    'registration_email' => Yii::t('amostranslation', 'Registration Email'),
    'registration_email_subject' => Yii::t('amostranslation', 'Registration Email Subject'),
    'confirm_registration' => Yii::t('amostranslation', 'Confirm Registration'),
    'confirm_registration_subject' => Yii::t('amostranslation', 'Confirm Registration Subject'),
    'info_waiting_list' => Yii::t('amostranslation', 'Info Waiting List'),
    'info_waiting_list_subject' => Yii::t('amostranslation', 'Info Waiting List Subject'),
    'webmeeting_webex' => Yii::t('amostranslation', 'Webmeeting Webex'),
    'webmeeting_webex_subject' => Yii::t('amostranslation', 'Webmeeting Webex Subject'),
    'webmeeting_webex_confirm_reg' => Yii::t('amostranslation', 'Webmeeting Webex Confirm Reg'),
    'webmeeting_webex_confirm_reg_subject' => Yii::t('amostranslation', 'Webmeeting Webex Confirm Reg Subject'),
    'webmeeting_webex_save_date' => Yii::t('amostranslation', 'Webmeeting Webex Save Date'),
    'webmeeting_webex_save_date_subject' => Yii::t('amostranslation', 'Webmeeting Webex Save Date Subject'),
    'send_tickets' => Yii::t('amostranslation', 'Send Tickets'),
    'send_tickets_subject' => Yii::t('amostranslation', 'Send Tickets Subject'),
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
    public function getEventEmailTemplates()
    {
    return $this->hasOne(\open20\amos\events\models\EventEmailTemplates::className(), ['id' => 'event_email_templates_id']);
    }
}
