<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `table_event_webex_accounts`.
 */
class m220531_115313_create_table_event_mailup_errors extends Migration
{
    const TABLE = "event_mailup_errors";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        if ($this->db->schema->getTableSchema(self::TABLE, true) === null)
        {
            $this->createTable(self::TABLE, [
                'id' => Schema::TYPE_PK,
                'email' => $this->string()->comment('Email'),
                'id_message' => $this->integer()->comment('Message'),
                'id_recipient' => $this->integer()->comment('Recipient'),
                'type' => $this->string()->comment('Error type'),
                'mailup_group_id' => $this->integer()->comment('Mailup group'),
                'models_classname_id' => $this->integer()->comment('Classname'),
                'record_id' => $this->integer()->comment('Record'),
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

        $this->addColumn('event_internal_list', 'errors_processed', $this->integer(1)->defaultValue(0)->after('mailup_error_message'));
        $this->addColumn('event_communication', 'errors_processed', $this->integer(1)->defaultValue(0)->after('mailup_error_message'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropTable(self::TABLE);
        $this->dropColumn('event_internal_list', 'errors_processed');
        $this->dropColumn('event_communication', 'errors_processed');


        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
