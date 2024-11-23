<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\events\models
 * @category   CategoryName
 */

namespace open20\amos\events\models;

use open20\amos\events\AmosEvents;
use yii\helpers\ArrayHelper;

/**
 * Class EventType
 * This is the model class for table "event_type".
 * @package open20\amos\events\models
 */
class EventWebexAccounts extends \open20\amos\events\models\base\EventWebexAccounts
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['email_webex'], 'required'],
            [['user_id'], 'integer'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'email_webex' => AmosEvents::txt('#email_webex'),
            'user_id' => AmosEvents::txt('#user_id'),
        ]);
    }

}
