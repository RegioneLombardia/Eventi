<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Index
 */
class m221012_123713_tuning extends Migration
{

    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->execute("
                ALTER TABLE `entitys_tags_mm`
                ADD INDEX `deleted_at_root_id_record_id` (`deleted_at`, `root_id`, `record_id`);

                ALTER TABLE `attach_file`
                ADD INDEX `name_item_id_attribute_model` (`name`, `item_id`, `attribute`, `model`);
            ");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "no revert";
    }
}