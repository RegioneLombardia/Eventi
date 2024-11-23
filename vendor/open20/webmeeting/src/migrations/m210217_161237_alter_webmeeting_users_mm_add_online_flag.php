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
class m210217_161237_alter_webmeeting_users_mm_add_online_flag extends Migration
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
            'online', 
            $this->integer(1)->defaultValue(null)->comment('Inviata email partecipazione WebEx meeting')->after('sent_email')
        );
        
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn($this->tableName, 'online');
    }

}
