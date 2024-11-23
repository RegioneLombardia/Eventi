<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m200329_092013_create_table_user_import_task extends Migration
{
    const TABLE = "{{%user_import_task}}";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        if ($this->db->schema->getTableSchema(self::TABLE, true) === null)
        {
            $this->createTable(self::TABLE, [
                'id' => Schema::TYPE_PK,
                'name' => $this->string()->comment('name'),
                'task_date' => $this->dateTime()->comment('Task Date'),
                'user_id' => $this->integer()->comment('User'),
                'event_group_referent_id' => $this->integer()->comment('Event Group Referent'),
                'tot_input_processed' => $this->integer()->comment('Tot Record Processed'),
                'tot_input_imported' => $this->integer()->comment('Tot Record Imported'),
                'status' => $this->integer()->comment('Tot Record Imported'),
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
        $this->addForeignKey('fk_event_group_referent_id_id1',self::TABLE, 'event_group_referent_id', 'event_group_referent', 'id');
        


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropForeignKey('fk_event_group_referent_id_id1', self::TABLE);
        $this->dropTable(self::TABLE);

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
