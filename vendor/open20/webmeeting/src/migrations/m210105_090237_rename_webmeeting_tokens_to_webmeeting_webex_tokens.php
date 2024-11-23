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
class m210105_090237_rename_webmeeting_tokens_to_webmeeting_webex_tokens extends Migration
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
        $this->tableName = '{{%webmeeting_tokens}}';
        $this->tableOptions = null;
    }

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->renameTable(
            $this->tableName, 
            'webmeeting_webex_tokens'
        );
        
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->renameTable(
            'webmeeting_webex_tokens',
            $this->tableName
        );
    }

}
