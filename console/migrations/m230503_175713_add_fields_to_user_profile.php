<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m230503_175713_add_fields_to_user_profile extends Migration
{


    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->addColumn('user_profile','dg_of_registration', $this->integer()->defaultValue(null)->after('user_import_task_id'));


    }


    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('user_profile','dg_of_registration');


    }
}
