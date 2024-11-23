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
class m201130_160737_alter_webmeeting_add_meetingId extends Migration
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
            'meetingId', 
            $this->char(255)->defaultValue(null)->comment('WebEx meetingId')->after('id')
        );
        
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn($this->tableName, 'meetingId');
    }

}
