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
class m201127_093200_create_webmeeting_team extends AmosMigrationTableCreation
{
    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%webmeeting_team}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'teamId' => $this->char(128)->defaultValue(null)->comment('Team id returned by WebEx on create'),
            'name' => $this->char(255)->defaultValue(null)->comment('Team name'),
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

}
