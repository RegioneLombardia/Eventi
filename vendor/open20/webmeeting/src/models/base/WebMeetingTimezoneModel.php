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
 * This is the class for controller "WebMeetingTimezoneModel".
 *
 * This is the base-model class for table "webmeeting_timezone".
 * @property integer $id
 * @property string  $tz_value
 * @property string  $timezone
 * 
 * @property string  $created_at
 * @property string  $updated_at
 * @property string  $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * 
 * @property \open20\webmeeting\models\WebMeetingModel $publishedUserProfile
 * @property \open20\webmeeting\models\WebMeetingModel $invitees
**/
class WebMeetingTimezoneModel extends ContentModel
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'webmeeting_timezone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timezone', 'tz_value'], 'string', 'max' => 255],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tz_value' => WebMeetingModule::_t('#idx_tz_value'),
            'timezone' => WebMeetingModule::_t('#idx_timezone'),
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
