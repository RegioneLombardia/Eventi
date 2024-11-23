<?php

namespace open20\amos\events\models\base;

use open20\amos\core\user\User;
use Yii;

/**
 * This is the base-model class for table "event_change_attributes".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $event_id
 * @property integer $operation_type
 * @property integer $operation_group
 * @property string $model_classname
 * @property integer $model_id
 * @property string $model_attribute
 * @property string $old_value
 * @property string $new_value
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\events\models\Event $event
 * @property \open20\amos\events\models\User $user
 */
class  EventChangeAttributes extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    const TYPE_CHANGE = 1;
    const TYPE_DELETE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_change_attributes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'event_id', 'operation_type', 'operation_group', 'model_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['model_classname', 'model_attribute', 'old_value', 'new_value'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
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
            'user_id' => Yii::t('amosevents', 'User'),
            'event_id' => Yii::t('amosevents', 'Event'),
            'operation_type' => Yii::t('amosevents', 'Operation type'),
            'operation_group' => Yii::t('amosevents', 'Operation Group'),
            'model_classname' => Yii::t('amosevents', 'Model classname'),
            'model_id' => Yii::t('amosevents', 'Model record id'),
            'model_attribute' => Yii::t('amosevents', 'Attribute'),
            'old_value' => Yii::t('amosevents', 'Old value'),
            'new_value' => Yii::t('amosevents', 'New Value'),
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
