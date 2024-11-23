<?php

namespace open20\amos\events\models\base;

use Yii;

/**
* This is the base-model class for table "event_highlights".
*
    * @property integer $id
    * @property integer $event_id
    * @property integer $n_order
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    *
            * @property \open20\amos\events\models\Event $event
    */
 class  EventHighlights extends \open20\amos\core\record\Record
{
    public $isSearch = false;

/**
* @inheritdoc
*/
public static function tableName()
{
return 'event_highlights';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['event_id', 'n_order', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
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
    'n_order' => Yii::t('amosevents', 'N order'),
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
}
