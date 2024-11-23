<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211122_180713_add_column_event_multilanguage extends Migration
{
    const TABLE_EVENT = "event";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_EVENT, 'multilanguage', $this->integer(1)->defaultValue(0)->after('publish_on_prl'));

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE_EVENT, 'multilanguage');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
