<?php

use yii\db\Migration;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200519_150813_add_field_user_profile extends Migration
{
    const TABLE = "{{%user_import_task}}";

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('user_profile', 'user_import_task_id', $this->integer());
        $this->addForeignKey('fk_userprofile_user_import_task_id1', 'user_profile', 'user_import_task_id', 'user_import_task', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropForeignKey('fk_userprofile_user_import_task_id1', 'user_profile');
        $this->dropColumn('user_profile', 'user_import_task_id');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}