<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `table_event_webex_accounts`.
 */
class m220920_155513_add_column_event_push_notification_subtype extends Migration
{
    const TABLE = "event_push_notification";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->addColumn(self::TABLE, 'subtype', $this->string()->comment('Subtype')->after('type'));

    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropColumn(self::TABLE, 'subtype');

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
