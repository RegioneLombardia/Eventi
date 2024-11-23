<?php

use yii\db\Migration;

class m171212_120605_create_tables_groups extends Migration
{

    const TABLE_GROUPS =  'groups';
    const TABLE_MEMBERS =  'groups_members';


    public function safeUp()
    {
        if ($this->db->schema->getTableSchema(self::TABLE_GROUPS, true) === null) {
            $this->createTable(self::TABLE_GROUPS, [
                'id' => $this->primaryKey(11),
                'parent_id' => $this->integer()->defaultValue(null)->comment('Parent'),
                'name' => $this->string()->defaultValue(null)->comment('Name'),
                'description' => $this->string()->defaultValue(null)->comment('Description'),
                'created_at' => $this->dateTime()->defaultValue(null)->comment('Created at'),
                'updated_at' => $this->dateTime()->defaultValue(null)->comment('Updated at'),
                'deleted_at' => $this->dateTime()->defaultValue(null)->comment('Deleted at'),
                'created_by' => $this->integer(11)->defaultValue(null)->comment('Created at'),
                'updated_by' => $this->integer(11)->defaultValue(null)->comment('Updated by'),
                'deleted_by' => $this->integer(11)->defaultValue(null)->comment('Deleted by'),
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
            $this->createIndex('idx_groups_parent_id', self::TABLE_GROUPS, 'parent_id');
        } else {
            echo "Table already exist': " . self::TABLE_GROUPS;
        }

        if ($this->db->schema->getTableSchema(self::TABLE_MEMBERS, true) === null) {
            $this->createTable(self::TABLE_MEMBERS, [
                'id' => $this->primaryKey(11),
                'groups_id' => $this->integer()->notNull()->comment('Group'),
                'user_id' => $this->integer()->notNull()->comment('User'),
                'description' => $this->string()->defaultValue(null)->comment('Description'),
                'created_at' => $this->dateTime()->defaultValue(null)->comment('Created at'),
                'updated_at' => $this->dateTime()->defaultValue(null)->comment('Updated at'),
                'deleted_at' => $this->dateTime()->defaultValue(null)->comment('Deleted at'),
                'created_by' => $this->integer(11)->defaultValue(null)->comment('Created at'),
                'updated_by' => $this->integer(11)->defaultValue(null)->comment('Updated by'),
                'deleted_by' => $this->integer(11)->defaultValue(null)->comment('Deleted by'),
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
            $this->addForeignKey('fk_groups_members_groups_id1', self::TABLE_MEMBERS, 'groups_id', 'groups', 'id');
            $this->addForeignKey('fk_groups_members_user_id1', self::TABLE_MEMBERS, 'user_id', 'user', 'id');
        } else {
            echo "Table already exist': " . self::TABLE_MEMBERS;
        }
        return true;
    }

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema(self::TABLE_GROUPS, true) !== null) {
            $this->execute("SET FOREIGN_KEY_CHECKS = 0");
            $this->dropTable(self::TABLE_GROUPS);
            $this->execute("SET FOREIGN_KEY_CHECKS = 1");
        } else {
            echo "Table doesn't exist";
        }

        if ($this->db->schema->getTableSchema(self::TABLE_MEMBERS, true) !== null) {
            $this->execute("SET FOREIGN_KEY_CHECKS = 0");
            $this->dropTable(self::TABLE_MEMBERS);
            $this->execute("SET FOREIGN_KEY_CHECKS = 1");
        } else {
            echo "Table doesn't exist";
        }
        return true;
    }
}
