<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200724_155113_add_column_user_profile_privacy_2 extends Migration
{



    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->addColumn('user_profile', 'privacy_2', $this->integer(1)->defaultValue(0)->after('privacy'));

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('user_profile', 'privacy_2');


    }
}
