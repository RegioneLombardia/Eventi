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
 * Class WebMeetingTokensModel 
 * This is the class for controller "WebMeetingTokensModel".
 *
 * This is the base-model class for table "webmeeting_tokens".
 * @property integer $id
 * @property string  $access_token
 * @property integer $expires_in
 * @property string  $refresh_token
 * @property integer $refresh_token_expires_in
 * @property string  $status                    // for worflow
 * 
 * @property string  $created_at
 * @property string  $updated_at
 * @property string  $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * 
**/
class WebMeetingTokenModel extends ContentModel
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'webmeeting_webex_tokens';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['access_token', 'refresh_token' ], 'string', 'max' => 255],
            [
                [
                    'access_token', 'expires_in', 'refresh_token', 'refresh_token_expires_in',
                    'created_at', 'updated_at', 'deleted_at', 'status'
                ],
                'safe'
            ],
            [
                [
                    'expires_in', 'refresh_token_expires_in',
                    'created_by', 'updated_by', 'deleted_by'
                ],
                'integer'
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
            'access_token' => WebMeetingModule::_t('#idx_access_token'),
            'expires_in' => WebMeetingModule::_t('#idx_expires_in'),
            'refresh_token' => WebMeetingModule::_t('#idx_refresh_token'),
            'refresh_token_expires_in' => WebMeetingModule::_t('#idx_refresh_token_expires_in'),
            'status' => WebMeetingModule::_t('#idx_status'),
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
