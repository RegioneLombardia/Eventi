<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211207_145713_add_column_landing_gallery_type extends Migration
{
    const TABLE_EVENT = "event_landing";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(self::TABLE_EVENT, 'gallery_type', $this->string()->after('image_slider_id'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');
        $this->dropColumn(self::TABLE_EVENT, 'gallery_type');
        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
