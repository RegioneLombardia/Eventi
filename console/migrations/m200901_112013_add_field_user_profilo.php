<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200901_112013_add_field_user_profilo extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('user_profile', 'disactivate_at', $this->dateTime()->after('attivo'));

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn('user_profile', 'disactivate_at');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
