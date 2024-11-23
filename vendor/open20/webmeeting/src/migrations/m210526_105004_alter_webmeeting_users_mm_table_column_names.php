<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Migrations
 */

use yii\db\Migration;

/**
 * m201014_123300_create_webmeeting_table
 * Handles the creation of table `{{%webmeeting}}`.
 */
class m210526_105004_alter_webmeeting_users_mm_table_column_names extends Migration
{
    /**
     * @var type 
     */
    protected $tableName;
    
    /**
     * @var type 
     */
    protected $tableOptions;
    
    /**
     * @inheritdoc
     */
    public function init() {
        $this->tableName = '{{%webmeeting_users_mm}}';
        $this->tableOptions = null;
    }

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->renameColumn($this->tableName, 'meetingId', 'meeting_id');
        $this->renameColumn($this->tableName, 'meetingInviteeId', 'meeting_invitee_id');
        $this->renameColumn($this->tableName, 'displayName', 'display_name');
        $this->renameColumn($this->tableName, 'coHost', 'co_host');
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->renameColumn($this->tableName, 'meeting_id', 'meetingId');
        $this->renameColumn($this->tableName, 'meeting_invitee_id', 'meetingInviteeId');
        $this->renameColumn($this->tableName, 'display_name', 'displayName');
        $this->renameColumn($this->tableName, 'co_host', 'coHost');
    }

}
