<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `table_event_webex_accounts`.
 */
class m220906_155513_add_column_event_communication_changed_info extends Migration
{
    const TABLE = "event_communication";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->addColumn(self::TABLE, 'is_changed_info', $this->integer(1)->comment('internal list')->after('communication_type'));

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropColumn(self::TABLE, 'is_changed_info');

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
