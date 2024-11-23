<?php

namespace open20\amos\events\models\base;

use Yii;

/**
 * This is the base-model class for table "event_mailup_errors".
 *
 * @property integer $id
 * @property string $email
 * @property integer $id_message
 * @property integer $id_recipient
 * @property integer $type
 * @property integer $mailup_group_id
 * @property integer $models_classname_id
 * @property integer $record_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 */
class  EventMailupErrors extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_mailup_errors';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_message', 'id_recipient', 'type', 'mailup_group_id', 'models_classname_id', 'record_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['email'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amosevents', 'ID'),
            'email' => Yii::t('amosevents', 'Email'),
            'id_message' => Yii::t('amosevents', 'Message'),
            'id_recipient' => Yii::t('amosevents', 'Recipient'),
            'type' => Yii::t('amosevents', 'Error type'),
            'mailup_group_id' => Yii::t('amosevents', 'Mailup group'),
            'models_classname_id' => Yii::t('amosevents', 'Classname'),
            'record_id' => Yii::t('amosevents', 'Record'),
            'created_at' => Yii::t('amosevents', 'Created at'),
            'updated_at' => Yii::t('amosevents', 'Updated at'),
            'deleted_at' => Yii::t('amosevents', 'Deleted at'),
            'created_by' => Yii::t('amosevents', 'Created by'),
            'updated_by' => Yii::t('amosevents', 'Updated at'),
            'deleted_by' => Yii::t('amosevents', 'Deleted at'),
        ];
    }
}
