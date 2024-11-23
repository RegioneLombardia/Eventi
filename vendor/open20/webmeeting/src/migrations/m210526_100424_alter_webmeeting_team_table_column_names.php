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
 * m201014_123300_create_webmeeting_table
 * Handles the creation of table `{{%webmeeting}}`.
 */
class m210526_100424_alter_webmeeting_team_table_column_names extends Migration
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
        $this->tableName = '{{%webmeeting_team}}';
        $this->tableOptions = null;
    }

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->renameColumn($this->tableName, 'teamId', 'team_id');
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->renameColumn($this->tableName, 'team_id', 'teamId');
    }

}
