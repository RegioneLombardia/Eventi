<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `table_event_webex_accounts`.
 */
class m220704_115313_create_table_event_changes_updates extends Migration
{
    const TABLE = "event_change_attributes";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {


        if ($this->db->schema->getTableSchema(self::TABLE, true) === null)
        {
            $this->createTable(self::TABLE, [
                'id' => Schema::TYPE_PK,
                'user_id' => $this->integer()->comment('User'),
                'event_id' => $this->integer()->comment('Event'),
                'operation_type' => $this->integer()->comment('Operation type'),
                'operation_group' => $this->string()->comment('Operation Group'),
                'model_classname' => $this->string()->comment('Model classname'),
                'model_id' => $this->integer()->comment('Model record id'),
                'model_attribute' => $this->string()->comment('Attribute'),
                'old_value' => $this->string()->comment('Old value'),
                'new_value' => $this->string()->comment('New Value'),
                'created_at' => $this->dateTime()->comment('Created at'),
                'updated_at' =>  $this->dateTime()->comment('Updated at'),
                'deleted_at' => $this->dateTime()->comment('Deleted at'),
                'created_by' =>  $this->integer()->comment('Created by'),
                'updated_by' =>  $this->integer()->comment('Updated at'),
                'deleted_by' =>  $this->integer()->comment('Deleted at'),
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);
        }
        else
        {
            echo "Nessuna creazione eseguita in quanto la tabella esiste gia'";
        }

        $this->addForeignKey('fk_event_change_attributes_event_id2', self::TABLE, 'event_id', 'event', 'id');
        $this->addForeignKey('fk_event_change_attributes_user_id2', self::TABLE, 'user_id', 'user', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropForeignKey('fk_event_change_attributes_event_id2', self::TABLE);
        $this->dropForeignKey('fk_event_change_attributes_user_id2', self::TABLE);
        $this->dropTable(self::TABLE);

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
