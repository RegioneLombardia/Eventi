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
class m210702_140224_alter_webmeeting_title_field_size extends Migration
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
        $this->alterColumn($this->tableName, 'title', $this->char(128));
        $this->alterColumn($this->tableName, 'host_email', $this->char(255));
        $this->alterColumn($this->tableName, 'host_key', $this->char(255));
        $this->alterColumn($this->tableName, 'site_url', $this->char(255));
        $this->alterColumn($this->tableName, 'web_link', $this->char(255));
        $this->alterColumn($this->tableName, 'allow_any_user_to_be_co_host', $this->integer(1));
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {}

}
