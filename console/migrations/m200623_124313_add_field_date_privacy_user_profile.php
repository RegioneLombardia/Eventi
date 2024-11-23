<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200623_124313_add_field_date_privacy_user_profile extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('user_profile', 'date_privacy', $this->dateTime()->after('privacy'));

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropColumn('user_profile', 'date_privacy');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
