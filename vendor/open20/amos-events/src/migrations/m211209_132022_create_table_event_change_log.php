<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `event_group_referent`.
 */
class m211209_132022_create_table_event_change_log extends Migration
{
    const TABLE    = '{{%event_change_log}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) === null) {
            $this->createTable(self::TABLE,
                [
                'id' => Schema::TYPE_PK,
                'event_id' => $this->integer()->comment('Event'),
                'user_id' => $this->integer()->comment('User'),
                'update_models_classname_id' => $this->integer()->comment('Classname'),
                'update_record_id' => $this->integer()->comment('Record id'),
                'type_operation' => $this->string()->comment('Type operation'),
                'name' => $this->string()->comment('Name'),
                'description' => $this->text()->comment('Description'),
                'created_at' => $this->dateTime()->comment('Created at'),
                'updated_at' => $this->dateTime()->comment('Updated at'),
                'deleted_at' => $this->dateTime()->comment('Deleted at'),
                'created_by' => $this->integer()->comment('Created by'),
                'updated_by' => $this->integer()->comment('Updated at'),
                'deleted_by' => $this->integer()->comment('Deleted at'),
                ],
                $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB AUTO_INCREMENT=1'
                        : null);
        } else {
            echo "Nessuna creazione eseguita in quanto la tabella ".self::TABLE." esiste gia'";
        }



        $this->addForeignKey('fk_event_change_log_user_id1', self::TABLE, 'user_id', '{{%user}}', 'id');
        $this->addForeignKey('fk_event_change_log_event_id1', self::TABLE, 'event_id', '{{%event}}', 'id');
        $this->addForeignKey('fk_event_change_log_update_model_classname_id1', self::TABLE, 'update_models_classname_id', '{{%models_classname}}', 'id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_event_change_log_user_id1', self::TABLE);
        $this->dropForeignKey('fk_event_change_log_event_id1', self::TABLE);
        $this->dropForeignKey('fk_event_change_log_update_model_classname_id1', self::TABLE);
        $this->dropTable(self::TABLE);
    }
}