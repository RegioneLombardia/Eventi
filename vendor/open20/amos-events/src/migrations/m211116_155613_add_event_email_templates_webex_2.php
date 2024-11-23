<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211116_155613_add_event_email_templates_webex_2 extends Migration
{
    const TABLE_EVENT_TEMPLATE = "event_email_templates";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex_save_date_subject', $this->text()->after('webmeeting_webex_subject'));
        $this->addColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex_save_date', $this->text()->after('webmeeting_webex_subject'));

        $this->addColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex_confirm_reg_subject', $this->text()->after('webmeeting_webex_subject'));
        $this->addColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex_confirm_reg', $this->text()->after('webmeeting_webex_subject'));

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex_save_date_subject');
        $this->dropColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex_save_date');

        $this->dropColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex_confirm_reg_subject');
        $this->dropColumn(self::TABLE_EVENT_TEMPLATE, 'webmeeting_webex_confirm_reg');

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
