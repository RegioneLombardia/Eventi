<?php

namespace open20\amos\events\models\base;

use Yii;

/**
 * This is the base-model class for table "event_related".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $related_event_id
 * @property integer $n_order
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 */
class  EventRelated extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_related';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['n_order', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['event_id', 'related_event_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('amoscommunity', 'ID'),
            'event_id' => Yii::t('amoscommunity', 'Event'),
            'related_event_id' => Yii::t('amoscommunity', 'Related event'),
            'created_at' => Yii::t('amoscommunity', 'Created at'),
            'updated_at' => Yii::t('amoscommunity', 'Updated at'),
            'deleted_at' => Yii::t('amoscommunity', 'Deleted at'),
            'created_by' => Yii::t('amoscommunity', 'Created by'),
            'updated_by' => Yii::t('amoscommunity', 'Updated at'),
            'deleted_by' => Yii::t('amoscommunity', 'Deleted at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventRelated()
    {
        return $this->hasOne(\open20\amos\events\models\Event::className(), ['id' => 'related_event_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventParent()
    {
        return $this->hasOne(\open20\amos\events\models\Event::className(), ['id' => 'event_id']);
    }


}
