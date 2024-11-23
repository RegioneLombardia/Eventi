<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * Handles the creation of table `event_group_referent`.
 */
class m210513_112042_create_table_log_cookie extends Migration
{
    const TABLE = '{{%log_cookie}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if ($this->db->schema->getTableSchema(self::TABLE, true) === null) {
            $this->createTable(self::TABLE,
                [
                    'id' => Schema::TYPE_PK,
                    'code' => $this->string()->comment('Code'),
                    'name' => $this->string()->comment('Name'),
                    'value' => $this->text()->comment('Value'),
                    'expire_date_cookie' => $this->dateTime()->comment('Expire date cookie'),
                    'expire_date_server' => $this->dateTime()->comment('Expire date server'),
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
            echo "Nessuna creazione eseguita in quanto la tabella " . self::TABLE . " esiste gia'";
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable(self::TABLE);
    }
}