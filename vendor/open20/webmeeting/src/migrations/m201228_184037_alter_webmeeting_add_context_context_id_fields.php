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
class m201228_184037_alter_webmeeting_add_context_context_id_fields extends Migration
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
            'context',
            $this->char(255)->defaultValue(null)->comment('Context for WebMeeting, null = platform or community, event and so on')->after('id')
        );
        
        $this->addColumn(
            $this->tableName,
            'context_id',
            $this->integer(11)->defaultValue(null)->comment('Context ID, used to find Community or Event WebMeeting}')->after('context')
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn($this->tableName, 'context');
        $this->dropColumn($this->tableName, 'context_id');
    }

}
