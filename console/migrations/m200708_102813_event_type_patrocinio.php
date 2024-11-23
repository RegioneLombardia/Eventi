<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200708_102813_event_type_patrocinio extends Migration
{



    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->update('event_type', ['patronage' => 1],[
            'title' => "Patrocinio",
            "description" => "Tutti possono vederlo, non è richiesta registrazione ma solo una email per inviare le informazioni specifiche." ,
            'limited_seats' => false,
            "enabled" => true,
            'event_type' => \open20\amos\events\models\EventType::TYPE_INFORMATIVE
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('event_type', ['patronage' => 0],[
            'title' => "Patrocinio",
            "description" => "Tutti possono vederlo, non è richiesta registrazione ma solo una email per inviare le informazioni specifiche." ,
            'limited_seats' => false,
            "enabled" => true,
            'event_type' => \open20\amos\events\models\EventType::TYPE_INFORMATIVE
        ]);

    }
}
