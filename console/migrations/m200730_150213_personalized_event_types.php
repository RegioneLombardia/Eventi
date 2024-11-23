<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200730_150213_personalized_event_types extends Migration
{



    /**
     * @inheritdoc
     */
    public function safeUp()
    {


        $this->update('event_type', ['title' => "Evento privato (su invito)"],['title' => "Su invito (evento privato)"] );
        $this->update('event_type', ['title' => "Evento privato (su invito) con posti limitati"],['title' => "Su invito (evento privato) con posti limitati"] );


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return true;

    }
}
