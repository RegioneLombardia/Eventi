<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200618_102513_update_order_event_type extends Migration
{
    const TABLE_EVENT_TYPE = "event_type";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update(self::TABLE_EVENT_TYPE,  ['order' => 1],['id' => 100]); // informativo
        $this->update(self::TABLE_EVENT_TYPE,  ['order' => 2],['id' => 104]); // pubblico
        $this->update(self::TABLE_EVENT_TYPE,  ['order' => 3],['id' => 101]); //pubblico limitato
        $this->update(self::TABLE_EVENT_TYPE,  ['order' => 4],['id' => 102]); // invito
        $this->update(self::TABLE_EVENT_TYPE,  ['order' => 5],['id' => 103]); // invito limiatato
        $this->update(self::TABLE_EVENT_TYPE,  ['order' => 6],['id' => 105]); //patrocinio


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE_EVENT_TYPE, 'order');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
