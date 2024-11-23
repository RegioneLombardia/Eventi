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
class m211002_190324_alter_webmeeting_add_fields_for_reminder extends Migration
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
            'reminder_sent', 
            $this->integer(1)->defaultValue(null)->comment('Remainder sent')->after('status')
        );
        
        $this->addColumn(
            $this->tableName, 
            'notification_before_conference', 
            $this->integer()->defaultValue(null)->comment('Remainder sent')->after('status')
        );
        
        $this->addColumn(
            $this->tableName, 
            'end_date_hour', 
            $this->datetime()->defaultValue(null)->comment('Remainder sent')->after('status')
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn($this->tableName, 'reminder_sent');
        $this->dropColumn($this->tableName, 'notification_before_conference');
        $this->dropColumn($this->tableName, 'end_date_hour');
    }

}
