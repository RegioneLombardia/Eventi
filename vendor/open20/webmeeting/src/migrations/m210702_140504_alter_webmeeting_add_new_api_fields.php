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
 * Handles the creation of table `{{%amos_gamification_tool}}`.
 */
class m210702_140504_alter_webmeeting_add_new_api_fields extends Migration
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
        $table = Yii::$app->db->schema->getTableSchema($this->tableName);
        if (!isset($table->columns['enabled_join_before_host'])) {
            $this->addColumn(
                $this->tableName, 
                'enabled_join_before_host',
                $this->integer(1)->defaultValue(0)->comment('Whether or not to allow any attendee to join the meeting before the host joins the meeting')->after('site_url')
            );
        }
        if (!isset($table->columns['enable_connect_audio_before_host'])) {
            $this->addColumn(
                $this->tableName, 
                'enable_connect_audio_before_host',
                $this->integer(1)->defaultValue(0)->comment('Whether or not to allow any attendee to connect audio in the meeting before host joins the meeting')->after('site_url')
            );
        }
        if (!isset($table->columns['join_before_host_minutes'])) {
            $this->addColumn(
                $this->tableName, 
                'join_before_host_minutes',
                $this->integer(1)->defaultValue(0)->comment('the number of minutes an attendee can join the meeting before the meeting start time and the host joins. Valid options are 0, 5, 10 and 15. Default is 0 if not specified')->after('site_url')
            );
        }

        if (!isset($table->columns['exclude_password'])) {
            $this->addColumn(
                $this->tableName, 
                'exclude_password',
                $this->integer(1)->defaultValue(0)->comment('Whether or not to exclude password from the meeting email invitation')->after('site_url')
            );
        }

        if (!isset($table->columns['public_meeting'])) {
            $this->addColumn(
                $this->tableName, 
                'public_meeting',
                $this->integer(1)->defaultValue(0)->comment('Whether or not to allow the meeting to be listed on the public calendar')->after('site_url')
            );
        }

        if (!isset($table->columns['reminder_time'])) {
            $this->addColumn(
                $this->tableName, 
                'reminder_time',
                $this->integer(1)->defaultValue(0)->comment('The number of minutes before the meeting begins, for sending an email reminder to the host')->after('site_url')
            );
        }

        if (!isset($table->columns['allow_first_user_to_be_co_host'])) {
            $this->addColumn(
                $this->tableName, 
                'allow_first_user_to_be_co_host',
                $this->integer(1)->defaultValue(0)->comment('Whether or not to allow the first attendee of the meeting with a host account on the target site to become a cohost')->after('site_url')
            );
        }

        if (!isset($table->columns['allow_authenticated_devices'])) {
            $this->addColumn(
                $this->tableName, 
                'allow_authenticated_devices',
                $this->integer(1)->defaultValue(0)->comment('Whether or not to allow authenticated video devices in the meeting\'s organization to start or join the meeting without a prompt')->after('site_url')
            );
        }

        if (!isset($table->columns['send_email'])) {
            $this->addColumn(
                $this->tableName, 
                'send_email',
                $this->integer(1)->defaultValue(0)->comment('Whether or not to send emails to host and invitees. It is an optional field and default value is true (on webex)')->after('site_url')
            );
        }
/*
        $this->addColumn(
            $this->tableName, 
            'host_email',
            $this->integer(1)->defaultValue(0)->comment('Whether or not to send emails to host and invitees. It is an optional field and default value is true (on webex)')->after('site_url')
        ); */
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn($this->tableName, 'enabled_join_before_host');
        $this->dropColumn($this->tableName, 'enable_connect_audio_before_host');
        $this->dropColumn($this->tableName, 'join_before_host_minutes');
        $this->dropColumn($this->tableName, 'exclude_password');
        $this->dropColumn($this->tableName, 'public_meeting');
        $this->dropColumn($this->tableName, 'reminder_time');
        $this->dropColumn($this->tableName, 'allow_first_user_to_be_co_host');
        $this->dropColumn($this->tableName, 'allow_authenticated_devices');
        $this->dropColumn($this->tableName, 'send_email');
      //  $this->dropColumn($this->tableName, 'host_email');
    }

}
