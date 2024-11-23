<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\models\base
 * @category   CategoryName
 */

namespace open20\amos\events\models\base;

use open20\amos\events\AmosEvents;
use yii\helpers\ArrayHelper;

/**
 * Class EventType
 * This is the base-model class for table "event_type".
 *
 * @property integer $id
 * @property string $email_webex
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $deleted_by
 * @package open20\amos\events\models\base
 */
class EventWebexAccounts extends \open20\amos\core\record\Record
{
    /**
     * @var AmosEvents $eventsModule
     */
    public $eventsModule = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_webex_accounts';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->eventsModule = AmosEvents::instance();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email_webex'], 'required'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['user_id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['email_webex'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => AmosEvents::t('amosevents', 'ID'),
            'email_webex' => AmosEvents::t('amosevents', 'Email Webex'),
            'user_id' => AmosEvents::t('amosevents', 'Assigned user'),
            'created_at' => AmosEvents::t('amosevents', 'Created At'),
            'updated_at' => AmosEvents::t('amosevents', 'Updated At'),
            'deleted_at' => AmosEvents::t('amosevents', 'Deleted At'),
            'created_by' => AmosEvents::t('amosevents', 'Created By'),
            'updated_by' => AmosEvents::t('amosevents', 'Updated By'),
            'deleted_by' => AmosEvents::t('amosevents', 'Deleted By')
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventTypeContext()
    {
        return $this->hasOne($this->eventsModule->model('EventTypeContext'), ['id' => 'event_context_id']);
    }
}
