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
class m201226_072837_alter_webmeetinguser_mm_add_email_displayName_fields extends Migration
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
        $this->addColumn(
            $this->tableName,
            'email',
            $this->char(255)->defaultValue(null)->comment('User Email for external platform users')->after('user_id')
        );
        
        $this->addColumn(
            $this->tableName,
            'displayName',
            $this->char(255)->defaultValue(null)->comment('Display Named used into WebEx for external platform users')->after('email')
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn($this->tableName, 'email');
        $this->dropColumn($this->tableName, 'displayName');
    }

}
