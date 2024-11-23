<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Migrations
 */

use yii\db\Migration;

/**
 * m201014_123300_create_webmeeting_table
 * Handles the creation of table `{{%webmeeting}}`.
 */
class m201014_123300_create_webmeeting_table extends Migration
{
    /**
     * @var type 
     */
    protected $tableName;
    
    /**
     * @var type 
     */
    protected $tableOptions;
    
    /**
     * @inheritdoc
     */
    public function init() {
        $this->tableName = '{{%webmeeting}}';
        $this->tableOptions = null;
    }

    /**
     * @inheritdoc
     */
    public function safeUp() {
        if ($this->db->driverName === 'mysql') {
            $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        if ($this->db->schema->getTableSchema($this->tableName, true) === null) {
            $this->createTable(
                $this->tableName,
                [
                    'id' => $this->primaryKey(),
                    'title' => $this->char(255)->defaultValue(null)->comment('Title'),
                    'description' => $this->text()->defaultValue(null)->comment("Description on WebMeeting to create"),
                    'id_room' => $this->char(255)->defaultValue(null)->comment('WebEx room'),
                    'created_at' => $this->dateTime()->defaultValue(null)->comment('Created at'),
                    'updated_at' => $this->dateTime()->defaultValue(null)->comment('Updated at'),
                    'deleted_at' => $this->dateTime()->defaultValue(null)->comment('Deleted at'),
                    'created_by' => $this->integer(11)->defaultValue(null)->comment('Created by'),
                    'updated_by' => $this->integer(11)->defaultValue(null)->comment('Updated by'),
                    'deleted_by' => $this->integer(11)->defaultValue(null)->comment('Deleted by')
                ],
                $this->tableOptions
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropTable($this->tableName);
    }
    
}