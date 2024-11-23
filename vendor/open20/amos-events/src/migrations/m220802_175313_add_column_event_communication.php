<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `table_event_webex_accounts`.
 */
class m220802_175313_add_column_event_communication extends Migration
{
    const TABLE = "event_communication";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->addColumn(self::TABLE, 'event_internal_list_id', $this->integer()->comment('internal list')->after('communication_type'));
        $this->addForeignKey('fk_event_communication_list_id1', self::TABLE, 'event_internal_list_id', 'event_internal_list', 'id');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropForeignKey('fk_event_communication_list_id1', self::TABLE);
        $this->dropColumn(self::TABLE, 'event_internal_list_id');

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
