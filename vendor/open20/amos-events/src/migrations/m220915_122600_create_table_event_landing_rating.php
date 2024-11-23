<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `event_rating`.
 */
class m220915_122600_create_table_event_landing_rating extends Migration
{
    const TABLE = "event_landing_rating";

    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        if ($this->db->schema->getTableSchema(self::TABLE, true) === null) {
            $this->createTable(self::TABLE, [
                'id' => $this->primaryKey(),
                'event_id' => $this->integer()->defaultValue(null)->comment('Event ID'),
                'user_id' => $this->integer()->defaultValue(null)->comment('User ID'),
                'rating' => $this->integer()->defaultValue(0)->comment('Rating'),
                'ip' => $this->text()->null()->defaultValue(null),

                'created_at' => $this->dateTime()->comment('Created at'),
                'updated_at' => $this->dateTime()->comment('Updated at'),
                'deleted_at' => $this->dateTime()->comment('Deleted at'),
                'created_by' => $this->integer()->comment('Created by'),
                'updated_by' => $this->integer()->comment('Updated at'),
                'deleted_by' => $this->integer()->comment('Deleted at'),
            ], $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB AUTO_INCREMENT=1' : null);

            $this->addForeignKey('fk_event_rating_event_id', self::TABLE, 'event_id', 'event', 'id');
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
