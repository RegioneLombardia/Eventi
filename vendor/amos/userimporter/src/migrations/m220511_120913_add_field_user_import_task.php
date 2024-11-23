<?php

use yii\db\Migration;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m220511_120913_add_field_user_import_task extends Migration
{
    const TABLE = "{{%user_import_task}}";

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE, 'email_text', $this->text()->defaultValue(null)->after('tot_input_imported'));
        $this->addColumn(self::TABLE, 'email_subject', $this->text()->defaultValue(null)->after('tot_input_imported'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE, 'email_text');
        $this->dropColumn(self::TABLE, 'email_subject');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}