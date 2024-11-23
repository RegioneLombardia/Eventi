<?php

use yii\db\Migration;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200406_150813_add_field_user_importer extends Migration
{
    const TABLE = "{{%user_import_task}}";

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE, 'accept',
            $this->integer()->after('status'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE, 'accept');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}