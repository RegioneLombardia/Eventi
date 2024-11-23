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
 * Class WebMeetingInviteesModel 
 *
 * This is the base-model class for table "webmeeting_users_mm".
 * @property integer $id
 * @property string  $webmeeting_id
 * @property string  $user_id
 * @property string  $co_host
 * @property integer $sent_email
 * @property string  $email
 * @property string  $display_name
 * @property string  $meeting_invitee_id
 * @property string  $status
 * 
 * @property string  $created_at
 * @property string  $updated_at
 * @property string  $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * 
**/
class WebMeetingInviteeModel extends ContentModel
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'webmeeting_users_mm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'webmeeting_id', 'user_id', 'co_host', 'sent_email', 'email',
                    'display_name', 'meeting_invitee_id', 'status'
                ], 'safe'
            ],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [
                [
                    'webmeeting_id', 'user_id', 'sent_email',
                    'created_by', 'updated_by', 'deleted_by'
                ], 'integer'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'webmeeting_id' => WebMeetingModule::_t('#idx_webmeeting_id_ref'),
            'user_id' => WebMeetingModule::_t('#idx_user_id_ref'),
            'co_host' => WebMeetingModule::_t('#idx_cohost_flag'),
            'sent_email' => WebMeetingModule::_t('#idx_sent_email_flag'),
            'status' => WebMeetingModule::_t('#idx_users_mm_status'),
            'email' => WebMeetingModule::_t('#idx_user_email_for_external'),
            'display_name' => WebMeetingModule::_t('#idx_display_name_for_external'),
            'meeting_invitee_id' => WebMeetingModule::_t('#idx_meeting_invitee_id'),
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
    public function getDescription($truncate = null) {}

    /**
     *  @inheritdoc
     */
    public function getGridViewColumns() {}

    /**
     *  @inheritdoc
     */
    public function getTitle()
    {
        return $this->title;
    }
}
