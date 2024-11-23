<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211102_164813_add_event_webmeeting_webex_id extends Migration
{
    const TABLE_EVENT = "event";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_EVENT, 'webmeeting_webex_id', $this->integer()->after('community_id'));


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE_EVENT, 'webmeeting_webex_id');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
