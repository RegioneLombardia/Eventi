<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211109_155613_add_event_email_templates_webex extends Migration
{
    const TABLE_EVENT_TEMPLATE = "event_email_templates";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex_subject', $this->string()->after('info_waiting_list_subject'));
        $this->addColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex', $this->string()->after('info_waiting_list_subject'));

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex_subject');
        $this->dropColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
