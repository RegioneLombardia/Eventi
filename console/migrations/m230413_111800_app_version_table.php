<?php

use open20\amos\core\migration\AmosMigrationTableCreation;

/**
 * Class m221124_222500_edit_app_notifications_container_table
 */
class m230413_111800_app_version_table extends AmosMigrationTableCreation
{

    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%app_version}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'version' => $this->integer(11)->notNull()->comment('version'),
            'days_notice' => $this->integer(11)->notNull()->comment('days notice'),
            'from' => $this->dateTime()->comment('from time'),
            'version_name' => $this->string(255)->comment('version name'),
            'created_at' => $this->dateTime()->null()->defaultValue(null)->comment('Created at'),
            'updated_at' => $this->dateTime()->null()->defaultValue(null)->comment('Updated at'),
            'deleted_at' => $this->dateTime()->null()->defaultValue(null)->comment('Deleted at'),
            'created_by' => $this->integer()->null()->defaultValue(null)->comment('Created by'),
            'updated_by' => $this->integer()->null()->defaultValue(null)->comment('Updated by'),
            'deleted_by' => $this->integer()->null()->defaultValue(null)->comment('Deleted by'),
        ];
    }

}
