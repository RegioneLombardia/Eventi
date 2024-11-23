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
 * @property string  $team_id
 * @property string  $name
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
class WebMeetingTeamModel extends ContentModel
{
    /**
     * @inheritdoc
     */
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'webmeeting_team';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
            [['team_id', 'name'], 'string'],
            [['name'], 'required'],
            [['created_at', 'updated_at', 'deleted_at', 'status'], 'safe'],
            [['created_by', 'updated_by', 'deleted_by'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'team_id' => WebMeetingModule::_t('#idx_team_id'),
            'name' => WebMeetingModule::_t('#idx_team_name'),
            'status' => WebMeetingModule::_t('#idx_team_status'),
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
