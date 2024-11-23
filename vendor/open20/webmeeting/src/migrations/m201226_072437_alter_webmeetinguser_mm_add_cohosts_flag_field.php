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
class m201226_072437_alter_webmeetinguser_mm_add_cohosts_flag_field extends Migration
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
            'cohost',
            $this->integer(1)->defaultValue(null)->comment('WebMeeting Invitees {0 = invitee|1 = CoHost}')->after('user_id')
        );
        
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn($this->tableName, 'cohost');
    }

}
