<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211207_123713_enable_lang_eng extends Migration
{



    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->update('language',['status' => 1], ['language_id' => 'en-GB'] );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('language',['status' => 0], ['language_id' => 'en-GB'] );
    }
}
