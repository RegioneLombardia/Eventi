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
class m201215_180537_alter_webmeeting_add_invitees_webex_fields extends Migration
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
            'invitees',
            $this->text()->defaultValue(null)->comment('WebMeeting agenda')->after('allowAnyUserToBeCoHost')
        );
        
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->dropColumn($this->tableName, 'invitees');
    }

}
