<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211102_123713_event_type_webmeeting extends Migration
{



    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        ;

        $this->insert('event_type', [
            'title' => "Videoconferenza WebEx",
            "description" => "Videoconferenza tramite tecnologia Cisco Webex. Solo gli invitati possono registrarsi. Il numero massimo di utenti invitati che possono accedere alla videoconferenza è 1000. E' comunque possibile limitare il numero massimo di posti disponibili." ,
            'limited_seats' => true,
            'manage_subscritions_queue' => true,
            'order' => 6,
            'partners' => false,
            "color" => "#CB740B",
            "webmeeting_webex" => true,
            "event_context_id" => true,
            "enabled" => true,
            'event_type' => \open20\amos\events\models\EventType::TYPE_UPON_INVITATION
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {

    }
}
