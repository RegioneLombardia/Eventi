<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `event_rating`.
 */
class m230420_110600_create_table_event_landing_sections_order extends Migration
{
    const TABLE = "event_landing_section";

    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        if ($this->db->schema->getTableSchema(self::TABLE, true) === null) {
            $this->createTable(self::TABLE, [
                'id' => $this->primaryKey(),
                'event_landing_id' => $this->integer()->defaultValue(null)->comment('Landing'),
                'section' => $this->string()->defaultValue(null)->comment('Section'),
                'n_order' => $this->integer()->defaultValue(0)->comment('Order'),

                'created_at' => $this->dateTime()->comment('Created at'),
                'updated_at' => $this->dateTime()->comment('Updated at'),
                'deleted_at' => $this->dateTime()->comment('Deleted at'),
                'created_by' => $this->integer()->comment('Created by'),
                'updated_by' => $this->integer()->comment('Updated at'),
                'deleted_by' => $this->integer()->comment('Deleted at'),
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);

            $this->addForeignKey('fk:event_landing_section_landing_id', self::TABLE, 'event_landing_id', 'event_landing', 'id');
        } else {
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

        $this->execute('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
