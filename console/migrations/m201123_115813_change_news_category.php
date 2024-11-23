<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m201123_115813_change_news_category extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('news_categorie', ['notify_category' => true], ['id' => 1]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->update('news_categorie', ['notify_category' => false], ['id' => 1]);
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
