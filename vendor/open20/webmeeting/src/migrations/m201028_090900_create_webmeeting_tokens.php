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
class m201028_090900_create_webmeeting_tokens extends AmosMigrationTableCreation
{
    /**
     * @inheritdoc
     */
    protected function setTableName()
    {
        $this->tableName = '{{%webmeeting_tokens}}';
    }

    /**
     * @inheritdoc
     */
    protected function setTableFields()
    {
        $this->tableFields = [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->defaultValue(null)->comment('User id request token'),
            'state' => $this->char(255)->defaultValue(null)->comment('Token type'),
            'access_token' => $this->char(255)->defaultValue(null)->comment('WebEx access token'),
            'expires_in' => $this->integer(11)->defaultValue(null)->comment('WebEx expires time in seconds'),
            'refresh_token' => $this->char(255)->defaultValue(null)->comment('WebEx refresh access token'),
            'refresh_token_expires_in' => $this->integer(11)->defaultValue(null)->comment('WebEx expires time in seconds for refresh token'),
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
