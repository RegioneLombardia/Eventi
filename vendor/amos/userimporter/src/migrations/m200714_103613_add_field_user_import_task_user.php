<?php

use yii\db\Migration;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200714_103613_add_field_user_import_task_user extends Migration
{
    const TABLE = "{{%user_import_task_user}}";

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE, 'user_id', $this->integer()->after('expire_date'));
        $this->addForeignKey('fk_user_import_task_user_user_id1', self::TABLE, 'user_id', 'user', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropForeignKey('fk_user_import_task_user_user_id1', self::TABLE);
        $this->dropColumn(self::TABLE, 'user_id');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}