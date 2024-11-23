<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m211105_152013_create_table_event_invitation_specific_users extends Migration
{
    const TABLE = "event_invitation_specific_users";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        if ($this->db->schema->getTableSchema(self::TABLE, true) === null)
        {
            $this->createTable(self::TABLE, [
                'id' => Schema::TYPE_PK,
                'event_id' => $this->integer()->comment('Event'),
                'user_id' => $this->integer()->comment('User'),
                'email' => $this->string()->comment('Email'),
                'send_at' => $this->dateTime()->comment('Send at'),
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
        $this->addForeignKey('fk_event_invitation_specific_users_event_id1',self::TABLE, 'event_id', 'event', 'id');
        $this->addForeignKey('fk_event_invitation_specific_users_user_id1',self::TABLE, 'user_id', 'user', 'id');


    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropForeignKey('fk_event_invitation_specific_users_event_id1', self::TABLE);
        $this->dropForeignKey('fk_event_invitation_specific_users_user_id1',self::TABLE);

        $this->dropTable(self::TABLE);

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');

    }
}
