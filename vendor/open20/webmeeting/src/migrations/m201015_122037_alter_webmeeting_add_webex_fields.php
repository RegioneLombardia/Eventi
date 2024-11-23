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
class m201015_122037_alter_webmeeting_add_webex_fields extends Migration
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
        $this->addColumn(
            $this->tableName, 
            'password',
            $this->char(255)->defaultValue(null)->comment('WebMeeting password')->after('id_room')
        );
        $this->addColumn(
            $this->tableName,
            'agenda', 
            $this->char(255)->defaultValue(null)->comment('WebMeeting agenda')->after('id_room')
        );
        $this->addColumn(
            $this->tableName, 
            'start', 
            $this->dateTime()->defaultValue(null)->comment('WebMeeting start date')->after('id_room')
        );
        $this->addColumn(
            $this->tableName, 
            'end', 
            $this->dateTime()->defaultValue(null)->comment('WebMeeting end date')->after('id_room')
        );
        $this->addColumn(
            $this->tableName, 
            'timezone', 
            $this->char(255)->defaultValue(null)->comment('WebMeeting timezone')->after('id_room')
        );
        $this->addColumn(
            $this->tableName, 
            'recurrence', 
            $this->char(255)->defaultValue(null)->comment('WebMeeting recurrence: string like: FREQ=DAILY;INTERVAL=1;COUNT=10')->after('id_room')
        );
        $this->addColumn(
            $this->tableName, 
            'enabledAutoRecordMeeting', 
            $this->integer(1)->defaultValue(0)->comment('WebMeeting agenda')->after('id_room')
        );
        $this->addColumn(
            $this->tableName, 
            'allowAnyUserToBeCoHost', 
            $this->integer(0)->defaultValue(0)->comment('allowAnyUserToBeCoHost')->after('id_room')
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn($this->tableName, 'password');
        $this->dropColumn($this->tableName, 'agenda');
        $this->dropColumn($this->tableName, 'start');
        $this->dropColumn($this->tableName, 'end');
        $this->dropColumn($this->tableName, 'recurrence');
        $this->dropColumn($this->tableName, 'enabledAutoRecordMeeting');
        $this->dropColumn($this->tableName, 'allowAnyUserToBeCoHost');
    }

}
