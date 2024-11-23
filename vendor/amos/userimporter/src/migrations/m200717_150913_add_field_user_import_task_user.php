<?php

use yii\db\Migration;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200717_150913_add_field_user_import_task_user extends Migration
{
    const TABLE = "{{%user_import_task_user}}";

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE, 'to_send', $this->integer()->defaultValue(1)->after('expire_date'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE, 'to_send');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}