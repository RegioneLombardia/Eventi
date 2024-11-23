<?php

namespace open20\amos\events\models\base;

use Yii;

/**
* This is the base-model class for table "event_landing_section".
*
    * @property integer $id
    * @property integer $event_landing_id
    * @property string $section
    * @property integer $n_order
    * @property string $created_at
    * @property string $updated_at
    * @property string $deleted_at
    * @property integer $created_by
    * @property integer $updated_by
    * @property integer $deleted_by
    *
            * @property \open20\amos\events\models\EventLanding $eventLanding
    */
 class  EventLandingSection extends \open20\amos\core\record\Record
{
    public $isSearch = false;

/**
* @inheritdoc
*/
public static function tableName()
{
return 'event_landing_section';
}

/**
* @inheritdoc
*/
public function rules()
{
return [
            [['event_landing_id', 'n_order', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['section'], 'string', 'max' => 255],
            [['event_landing_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventLanding::className(), 'targetAttribute' => ['event_landing_id' => 'id']],
];
}

/**
* @inheritdoc
*/
public function attributeLabels()
{
return [
    'id' => Yii::t('amosevents', 'ID'),
    'event_landing_id' => Yii::t('amosevents', 'Landing'),
    'section' => Yii::t('amosevents', 'Section'),
    'n_order' => Yii::t('amosevents', 'Order'),
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
    public function getEventLanding()
    {
    return $this->hasOne(\open20\amos\events\models\EventLanding::className(), ['id' => 'event_landing_id']);
    }
}
