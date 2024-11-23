<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Models
 */

namespace open20\webmeeting\models;

use open20\webmeeting\WebMeetingModule;

use open20\amos\core\record\Record;
use Yii;

/**
 * Class WebMeetingUsersMm
 *
 * This is the base-model class for table "webmeeting_users_mm".
 *
 * @property integer  $id
 * @property string   $display_name
 * @property string   $email
 * @property integer  $co_host
 * @property string   $updated_at
 * @property string   $deleted_at
 * @property integer  $created_by
 * @property integer  $updated_by
 * @property integer  $deleted_by
 *
 */
class WebMeetingHostUsers extends Record
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'webmeeting_hosts_users';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['co_host', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['display_name', 'email', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'display_name' => WebMeetingModule::_t('#idx_host_display_name'),
            'email' => WebMeetingModule::_t('#idx_host_email'),
            'co_host' => WebMeetingModule::_t('#idx_host_cohost'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'deleted_at' => Yii::t('app', 'Deleted at'),
            'created_by' => Yii::t('app', 'Created by'),
            'updated_by' => Yii::t('app', 'Updated by'),
            'deleted_by' => Yii::t('app', 'Deleted by'),
        ];
    }
    
}
