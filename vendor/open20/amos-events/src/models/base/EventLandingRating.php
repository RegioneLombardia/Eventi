<?php

namespace open20\amos\events\models\base;

use Yii;

/**
 * This is the base-model class for table "event_landing_rating".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $user_id
 * @property integer $rating
 * @property string $ip
 * @property string $maturity
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 *
 * @property \open20\amos\events\models\Event $event
 */
class  EventLandingRating extends \open20\amos\core\record\Record
{
    public $isSearch = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_landing_rating';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'user_id', 'rating', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['ip'], 'string'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['maturity'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'user_id' => 'User ID',
            'rating' => 'Rating',
            'ip' => 'Ip',
            'maturity' => 'Maturity',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'deleted_by' => 'Deleted By',
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
