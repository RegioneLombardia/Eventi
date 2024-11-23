<?php

use yii\db\Migration;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m220531_145613_insert_models_classname2 extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('models_classname', [
            'classname' => \open20\amos\events\models\EventInternalList::className(),
            'module' => 'events',
            'table' => 'event_internal_list',
            'label' => 'Event internal list',
        ]);

        $this->insert('models_classname', [
            'classname' => \open20\amos\events\models\EventCommunication::className(),
            'module' => 'events',
            'table' => 'event_communication',
            'label' => 'Event communication',
        ]);

        $this->update('models_classname', ['table' => 'event_invitation'],
            [
                'classname' => \open20\amos\events\models\EventInvitation::className(),
            ]);

        $this->update('models_classname', ['table' => 'event_invitation_sent'],
            [
                'classname' => \open20\amos\events\models\EventInvitationSent::className(),
            ]);
        $this->update('models_classname', ['table' => 'event_communication_sent'],
            [
                'classname' => \open20\amos\events\models\EventCommunicationSent::className(),
            ]);




    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->delete('models_classname', [
            'classname' => \open20\amos\events\models\EventInternalList::className(),
            'module' => 'events',
            'label' => ' Event invitation',
        ]);

        $this->delete('models_classname', [
            'classname' => \open20\amos\events\models\EventCommunication::className(),
            'module' => 'events',
            'label' => 'Event internal list',
        ]);

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
