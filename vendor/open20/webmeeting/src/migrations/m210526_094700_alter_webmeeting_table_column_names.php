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
class m210526_094700_alter_webmeeting_table_column_names extends Migration
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
        $this->tableName = '{{%webmeeting}}';
        $this->tableOptions = null;
    }

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->renameColumn($this->tableName, 'meetingId', 'meeting_id');
        $this->renameColumn($this->tableName, 'allowAnyUserToBeCoHost', 'allow_any_user_to_be_co_host');
        $this->renameColumn($this->tableName, 'enabledAutoRecordMeeting', 'enabled_auto_record_meeting');
        $this->renameColumn($this->tableName, 'dialInIpAddress', 'dial_in_ip_address');
        $this->renameColumn($this->tableName, 'sipAddress', 'sip_address');
        $this->renameColumn($this->tableName, 'webLink', 'web_link');
        $this->renameColumn($this->tableName, 'siteUrl', 'site_url');
        $this->renameColumn($this->tableName, 'hostKey', 'host_key');
        $this->renameColumn($this->tableName, 'hostEmail', 'host_email');
        $this->renameColumn($this->tableName, 'hostDisplayName', 'host_display_name');
        $this->renameColumn($this->tableName, 'hostUserId', 'host_user_id');     
        $this->renameColumn($this->tableName, 'meetingType', 'meeting_type');
        $this->renameColumn($this->tableName, 'phoneAndVideoSystemPassword', 'phone_and_video_system_password');
        $this->renameColumn($this->tableName, 'meetingNumber', 'meeting_number');
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->renameColumn($this->tableName, 'meeting_id', 'meetingId');
        $this->renameColumn($this->tableName, 'allow_any_user_to_be_co_host', 'allowAnyUserToBeCoHost');
        $this->renameColumn($this->tableName, 'enabled_auto_record_meeting', 'enabledAutoRecordMeeting');
        $this->renameColumn($this->tableName, 'dial_in_ip_address', 'dialInIpAddress');
        $this->renameColumn($this->tableName, 'sip_address', 'sipAddress');
        $this->renameColumn($this->tableName, 'web_link', 'webLink');
        $this->renameColumn($this->tableName, 'site_url', 'siteUrl');
        $this->renameColumn($this->tableName, 'host_key', 'hostKey');
        $this->renameColumn($this->tableName, 'host_email', 'hostEmail');
        $this->renameColumn($this->tableName, 'host_display_name', 'hostDisplayName');
        $this->renameColumn($this->tableName, 'host_user_id', 'hostUserId');     
        $this->renameColumn($this->tableName, 'meeting_type', 'meetingType');
        $this->renameColumn($this->tableName, 'phone_and_video_system_password', 'phoneAndVideoSystemPassword');
        $this->renameColumn($this->tableName, 'meeting_number', 'meetingNumber');
    }

}
