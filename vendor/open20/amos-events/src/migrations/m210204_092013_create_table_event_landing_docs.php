<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `een_partnership_proposal`.
 */
class m210204_092013_create_table_event_landing_docs extends Migration
{
    const TABLE_LANDING_DOCS = "event_landing_documents";


    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        if ($this->db->schema->getTableSchema(self::TABLE_LANDING_DOCS, true) === null)
        {
            $this->createTable(self::TABLE_LANDING_DOCS, [
                'id' => Schema::TYPE_PK,
                'event_landing_id' => $this->integer()->comment('Landing'),
                'documenti_id' => $this->integer()->comment('Name'),
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
        $this->addForeignKey('fk_event_landing_documents_land_id1',self::TABLE_LANDING_DOCS, 'event_landing_id', 'event_landing', 'id');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropTable(self::TABLE_LANDING_DOCS);

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
