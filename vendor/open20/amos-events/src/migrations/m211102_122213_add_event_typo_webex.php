<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211102_122213_add_event_typo_webex extends Migration
{
    const TABLE_EVENT_TYPE = "event_type";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_EVENT_TYPE, 'webmeeting_webex', $this->integer(1)->defaultValue(0)->after('event_context_id'));


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE_EVENT_TYPE, 'webmeeting_webex');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
