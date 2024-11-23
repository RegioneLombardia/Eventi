<?php

use yii\db\Migration;

class m201020_120605_create_tables_groups_comunication extends Migration
{

    const TABLE_GROUPS_COMMUNICATION =  'groups_comunication';


    public function safeUp()
    {
        if ($this->db->schema->getTableSchema(self::TABLE_GROUPS_COMMUNICATION, true) === null) {
            $this->createTable(self::TABLE_GROUPS_COMMUNICATION, [
                'id' => $this->primaryKey(11),
                'groups_id' => $this->integer()->defaultValue(null)->comment('Group'),
                'subject' => $this->text()->defaultValue(null)->comment('Subject'),
                'text' => $this->text()->defaultValue(null)->comment('Text'),
                'sent_at' =>  $this->dateTime()->defaultValue(null)->comment('Sent at'),
                'status' =>  $this->integer()->defaultValue(1)->comment('Status'),
                'layout_path' => $this->string()->defaultValue(null)->comment('Layout'),
                'created_at' => $this->dateTime()->defaultValue(null)->comment('Created at'),
                'updated_at' => $this->dateTime()->defaultValue(null)->comment('Updated at'),
                'deleted_at' => $this->dateTime()->defaultValue(null)->comment('Deleted at'),
                'created_by' => $this->integer(11)->defaultValue(null)->comment('Created at'),
                'updated_by' => $this->integer(11)->defaultValue(null)->comment('Updated by'),
                'deleted_by' => $this->integer(11)->defaultValue(null)->comment('Deleted by'),
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
            $this->addForeignKey('fk_groups_comunication_group_id1', self::TABLE_GROUPS_COMMUNICATION, 'groups_id', 'groups', 'id');

        } else {
            echo "Table already exist': " . self::TABLE_GROUPS_COMMUNICATION;
        }

        return true;
    }

    public function safeDown()
    {
            $this->execute("SET FOREIGN_KEY_CHECKS = 0");
            $this->dropTable(self::TABLE_GROUPS_COMMUNICATION);
            $this->execute("SET FOREIGN_KEY_CHECKS = 1");

        return true;
    }
}
