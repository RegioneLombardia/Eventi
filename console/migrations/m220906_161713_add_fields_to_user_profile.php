<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m220906_161713_add_fields_to_user_profile extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->addColumn('user_profile','enable_email_change_event', $this->integer(1)->defaultValue(1)->after('privacy_2'));
        $this->addColumn('user_profile','enable_push_change_event', $this->integer(1)->defaultValue(1)->after('privacy_2'));


    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('user_profile','enable_email_change_event');
        $this->dropColumn('user_profile','enable_push_change_event');


    }
}
