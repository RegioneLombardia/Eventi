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
 * More info on
 * https://developer.webex.com/docs/api/v1/meetings/create-a-meeting
 * 
 * Handles the creation of table `{{%webmeeting}}`
 */
class m201130_202437_alter_webmeeting_add_others_webex_fields extends Migration
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
        $this->alterColumn(
            $this->tableName,
            'agenda',
            $this->text()->defaultValue(null)->comment('WebMeeting agenda')->after('id_room')
        );
        
        $this->addColumn(
            $this->tableName, 
            'meetingNumber', 
            $this->char(255)->defaultValue(null)->comment('WebEx meetingId')->after('status')
        );
        
        $this->addColumn(
            $this->tableName, 
            'phoneAndVideoSystemPassword', 
            $this->char(8)->defaultValue(null)->comment('8-digit numeric password to join meeting from audio and video devices.')->after('status')
        );
        
        $this->addColumn(
            $this->tableName, 
            'meetingType', 
            "ENUM('', 'meetingSeries', 'scheduledMeeting', 'meeting') DEFAULT NULL COMMENT 'WebEx Meeting meetingType' AFTER `status`"
        );
        
        $this->addColumn(
            $this->tableName, 
            'state', 
            "ENUM('', 'active', 'scheduled', 'ready', 'lobby', 'inProgress', 'ended', 'missed', 'expired') DEFAULT NULL COMMENT 'WebEx Meeting state' AFTER `status`"
        );

        $this->alterColumn(
            $this->tableName, 
            'timezone', 
            $this->char(32)->defaultValue(null)->comment('Time zone of start and end, conforming with the IANA time zone database')->after('status')
        );     
             
        $this->addColumn(
            $this->tableName, 
            'hostUserId', 
            $this->char(128)->defaultValue(null)->comment("It's in the format of Base64Encode(ciscospark://us/PEOPLE/hostUserId)")->after('status')
        );     
             
        $this->addColumn(
            $this->tableName, 
            'hostDisplayName', 
            $this->char(128)->defaultValue(null)->comment('Display name for meeting host')->after('status')
        );
        
        $this->addColumn(
            $this->tableName, 
            'hostEmail', 
            $this->char(128)->defaultValue(null)->comment('Email address for meeting host')->after('status')
        );

        $this->addColumn(
            $this->tableName, 
            'hostKey', 
            $this->char(128)->defaultValue(null)->comment('Email address for meeting host')->after('status')
        );

        $this->addColumn(
            $this->tableName, 
            'siteUrl', 
            $this->char(128)->defaultValue(null)->comment('Site URL for the meeting')->after('status')
        );

        $this->addColumn(
            $this->tableName, 
            'webLink', 
            $this->char(128)->defaultValue(null)->comment('Link to meeting information page where meeting client will be launched')->after('status')
        );

        $this->addColumn(
            $this->tableName, 
            'sipAddress', 
            $this->char(128)->defaultValue(null)->comment('SIP address for callback from a video system')->after('status')
        );
        
        $this->addColumn(
            $this->tableName, 
            'dialInIpAddress', 
            $this->char(128)->defaultValue(null)->comment('IP address for callback from a video system')->after('status')
        );

        $this->addColumn(
            $this->tableName, 
            'telephony', 
            $this->text()->defaultValue(null)->comment('Json Encoded with: accessCode, callInNumbers, links objects')->after('status')
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->alterColumn(
            $this->tableName,
            'agenda',
            $this->char(255)->defaultValue(null)->comment('WebMeeting agenda')->after('id_room')
        );
        
        $this->dropColumn($this->tableName, 'meetingNumber');
        $this->dropColumn($this->tableName, 'phoneAndVideoSystemPassword');
        $this->dropColumn($this->tableName, 'meetingType');
        $this->dropColumn($this->tableName, 'state');
        $this->dropColumn($this->tableName, 'hostUserId');
        $this->dropColumn($this->tableName, 'hostDisplayName');
        $this->dropColumn($this->tableName, 'hostEmail');
        $this->dropColumn($this->tableName, 'hostKey');
        $this->dropColumn($this->tableName, 'siteUrl');
        $this->dropColumn($this->tableName, 'webLink');
        $this->dropColumn($this->tableName, 'sipAddress');
        $this->dropColumn($this->tableName, 'dialInIpAddress');
        $this->dropColumn($this->tableName, 'telephony');
    }

}
