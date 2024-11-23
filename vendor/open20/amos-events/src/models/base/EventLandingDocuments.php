<?php

namespace open20\amos\events\models\base;

use open20\amos\documenti\models\Documenti;
use Yii;

/**
 * This is the base-model class for table "event_landing_documents".
 *
 * @property integer $id
 * @property integer $event_landing_id
 * @property integer $documenti_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\events\models\EventLanding $eventLanding
 */
class  EventLandingDocuments extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_landing_documents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_landing_id', 'documenti_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
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
            'documenti_id' => Yii::t('amosevents', 'Name'),
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumenti()
    {
        return $this->hasOne(Documenti::className(), ['id' => 'documenti_id']);
    }
}
