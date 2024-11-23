<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Models
 */

namespace open20\webmeeting\models\base;

use open20\webmeeting\WebMeetingModule;
use open20\amos\core\record\ContentModel;
use Yii;

/**
 * Class WebMeetingModel
 * This is the class for controller "WebMeetingModel".
 *
 * This is the base-model class for table "webmeeting".
 * @property integer $id
 * @property string  $context
 * @property integer $context_id
 * @property string  $title
 * @property string  $description
 * @property string  $id_room
 * @property string  $status
 *
 * Used by WebEx
 * @property string  $password
 * @property string  $agenda
 * @property string  $start
 * @property string  $end
 * @property string  $timezone
 * @property string  $recurrence
 * @property integer $enabled_auto_record_meeting
 * @property integer $allow_any_user_to_be_co_host
 *
 * Returned values from WebEx server on creation/update
 * @property string $meeting_id
 * @property string $meeting_number
 * @property string $meeting_type
 * @property string $state
 * @property string $timezone
 * @property string $host_user_id
 * @property string $host_display_name
 * @property string $host_email
 * @property string $host_key
 * @property string $site_url
 * @property string $web_link
 * @property string $sip_address
 * @property string $dial_in_ip_address
 * @property integer $enabled_auto_record_meeting
 * @property integer $allow_Any_user_to_be_co_host
 * @property string $invitees
 * @property string $telephony
 * @property string $reminder_time
 *
 * @property string  $created_at
 * @property string  $updated_at
 * @property string  $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\webmeeting\models\WebMeetingModel $published_user_profile
 * @property \open20\webmeeting\models\WebMeetingModel $co_hosts
 * */
class WebMeetingModel extends ContentModel
{
    public $isSearch = false;

    /**
     * 
     * @var type
     */
    public $end_date;
    public $end_hour;
    public $start_date;
    public $start_hour;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'webmeeting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'password', 'id_room', 'host_email'], 'string', 'max' => 255],
            [['meeting_id', 'context', 'description', 'agenda', 'timezone'], 'string'],
            [['title', 'password', 'start', 'end', 'host_email'], 'required'],
            [['start_date', 'end_date', 'start_hour', 'end_hour'], 'required'],
            [['context', 'context_id', 'start', 'end', 'reminder_time', 'created_at', 'updated_at', 'deleted_at', 'status', 'invitees'], 'safe'],
            [['context_id', 'recurrence', 'enabled_auto_record_meeting', 'allow_any_user_to_be_co_host', 'created_by', 'updated_by',
                'deleted_by'], 'integer'],
            [
                'start', 'compare', 'compareAttribute' => 'end', 'operator' => '<',
                'whenClient' => "function (attribute, value) {
                    var end_data = $('#end_date).val();
                    var end_hour = $('#end_hour).val();
                    $('#_date_end).val(end_data + ' ' + end_hour); 
                    return $('#_date_end').val() != '';
                }"
            ],
            [
                'end', 'compare', 'compareAttribute' => 'start', 'operator' => '>',
                'whenClient' => "function (attribute, value) {
                    var start_data = $('#start_date).val();
                    var start_hour = $('#start_hour).val();
                    $('#_date_begin).val(start_data + ' ' + start_hour); 
                    return $('#_date_begin').val() != '';
                }"

            ],
            [['meeting_id', 'dial_in_ip_address', 'sip_address', 'web_link', 'site_url', 'host_key', 'host_display_name',
                'host_user_id', 'state', 'meeting_type', 'phone_and_video_system_password', 'meeting_number', 'published_by'],
                'safe'],
            [['start', 'end'], 'checkDate'],
        ];
    }

    /**
     * 
     * @param type $attribute
     * @param type $params
     * @return boolean
     */
    public function checkDate($attribute, $params)
    {
        $isValid  = true;
        $dateTime = date('Y-m-d H:i');
        if ($this->$attribute < date('Y-m-d H:i')) {
            $isValid = false;
            $this->addError(
                $attribute,
                WebMeetingModule::_t('#bad_datetime_value',
                    [
                    'attribute' => $this->getAttributeLabel($attribute),
                    'dateTime' => $dateTime
                ])
            );
        }

        return $isValid;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'context' => WebMeetingModule::_t('#idx_context'),
            'context_id' => WebMeetingModule::_t('#idx_context_id'),
            'title' => WebMeetingModule::_t('#idx_title'),
            'description' => WebMeetingModule::_t('#idx_description'),
            'id_room' => WebMeetingModule::_t('#idx_room'),
            'host_email' => WebMeetingModule::_t('#idx_host_email'),
            'start' => WebMeetingModule::_t('#idx_start_datetime'),
            'end' => WebMeetingModule::_t('#idx_end_datetime'),
            'reminder_time' => WebMeetingModule::_t('#idx_reminder_time'),
            'allow_any_user_to_be_co_host' => WebMeetingModule::_t('#idx_allow_any_user_to_be_cohost'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted_at' => Yii::t('app', 'Deleted at'),
            'created_by' => Yii::t('app', 'Created by'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'deleted_by' => Yii::t('app', 'Deleted by'),
        ];
    }

    /**
     *  @inheritdoc
     */
    public function getGridViewColumns()
    {
        return [
            'id',
            'context',
            'context_id',
            'title',
            'description',
            'id_room',
            'host_email',
            'start',
            'end',
            'reminder_time',
            'allow_any_user_to_be_co_host',
        ];
    }

    /**
     *  @inheritdoc
     */
    public function getDescription($truncate = null)
    {
        return $this->description;
    }

    /**
     *  @inheritdoc
     */
    public function getTitle()
    {
        return $this->title;
    }
}