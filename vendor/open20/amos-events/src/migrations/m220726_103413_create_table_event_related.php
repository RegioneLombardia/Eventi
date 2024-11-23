<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `table_event_webex_accounts`.
 */
class m220726_103413_create_table_event_related extends Migration
{
    const TABLE = "event_related";

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
                'related_event_id' => $this->integer()->comment('Related event'),
                'n_order' => $this->integer()->comment('Order'),
                'created_at' => $this->dateTime()->comment('Created at'),
                'updated_at' =>  $this->dateTime()->comment('Updated at'),
                'deleted_at' => $this->dateTime()->comment('Deleted at'),
                'created_by' =>  $this->integer()->comment('Created by'),
                'updated_by' =>  $this->integer()->comment('Updated at'),
                'deleted_by' =>  $this->integer()->comment('Deleted at'),
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);

            $this->addColumn('event_landing','title_related_event', $this->string()->comment('Title related event')->after('title_presentation'));
            $this->addForeignKey('fk_event_related_event_id', self::TABLE, 'event_id', 'event', 'id');
            $this->addForeignKey('fk_event_related_related_event_id', self::TABLE, 'related_event_id', 'event', 'id');
        }
        else
        {
            echo "Nessuna creazione eseguita in quanto la tabella esiste gia'";
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0;');

        $this->dropTable(self::TABLE);
        $this->dropColumn('event_landing','title_related_event');

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
