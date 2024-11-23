<?php

namespace open20\amos\events\models\base;

use open20\amos\core\models\ModelsClassname;
use open20\amos\core\user\User;
use Yii;

/**
 * This is the base-model class for table "event_change_log".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $user_id
 * @property integer $update_models_classname_id
 * @property integer $update_record_id
 * @property string $type_operation
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\events\models\Event $event
 * @property ModelsClassname $updateModelsClassname
 * @property User $user
 */
class  EventChangeLog extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_change_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'user_id', 'update_models_classname_id', 'update_record_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['type_operation', 'name'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
            [['update_models_classname_id'], 'exist', 'skipOnError' => true, 'targetClass' => ModelsClassname::className(), 'targetAttribute' => ['update_models_classname_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amosevents', 'ID'),
            'event_id' => Yii::t('amosevents', 'Event'),
            'user_id' => Yii::t('amosevents', 'User'),
            'update_models_classname_id' => Yii::t('amosevents', 'Classname'),
            'update_record_id' => Yii::t('amosevents', 'Record id'),
            'type_operation' => Yii::t('amosevents', 'Type operation'),
            'name' => Yii::t('amosevents', 'Name'),
            'description' => Yii::t('amosevents', 'Description'),
            'created_at' => Yii::t('amosevents', 'Created at'),
            'updated_at' => Yii::t('amosevents', 'Updated at'),
            'deleted_at' => Yii::t('amosevents', 'Deleted at'),
            'created_by' => Yii::t('amosevents', 'Created by'),
            'updated_by' => Yii::t('amosevents', 'Updated at'),
            'deleted_by' => Yii::t('amosevents', 'Deleted at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\open20\amos\events\models\Event::className(), ['id' => 'event_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdateModelsClassname()
    {
        return $this->hasOne(ModelsClassname::className(), ['id' => 'update_models_classname_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
