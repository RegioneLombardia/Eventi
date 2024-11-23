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
class m210526_095724_alter_webmeeting_hosts_users_table_column_names extends Migration
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
        $this->tableName = '{{%webmeeting_hosts_users}}';
        $this->tableOptions = null;
    }

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->renameColumn($this->tableName, 'displayName', 'display_name');
        $this->renameColumn($this->tableName, 'coHost', 'co_host');
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->renameColumn($this->tableName, 'display_name', 'displayName');
        $this->renameColumn($this->tableName, 'co_host', 'coHost');
    }
}
