<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Migrations
 */

use open20\amos\core\migration\AmosMigrationTableCreation;

/**
 * Class m201015_114300_create_webmeeting_users_mm_table
 */
class m201015_114300_create_webmeeting_users_mm_table extends AmosMigrationTableCreation
{
    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%webmeeting_users_mm}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'webmeeting_id' => $this->integer(11)->defaultValue(null)->comment('WebMeeting id'),
            'user_id' => $this->integer(11)->defaultValue(null)->comment('user id'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function beforeTableCreation()
    {
        parent::beforeTableCreation();
        $this->setAddCreatedUpdatedFields(true);
    }

    /**
     * @inheritdoc
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey('fk_webmeeting_id', $this->getRawTableName(), 'webmeeting_id', '{{%webmeeting}}', 'id');
        $this->addForeignKey('fk_users_webmeeting_mm', $this->getRawTableName(), 'user_id', '{{%user}}', 'id');
    }

}
