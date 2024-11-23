<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Migrations
 */

use open20\webmeeting\widgets\icons\WidgetIconWebMeetingTeam;

use open20\amos\dashboard\models\AmosWidgets;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%webmeeting}}`.
 */
class m210203_165700_disable_webmeeting_team_widget extends Migration
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
        $this->tableName = AmosWidgets::tableName();
        $this->tableOptions = null;
    }

    /**
     * @inheritdoc
     */
    public function safeUp() {
        $this->update(
            $this->tableName,
            ['status' => 0],
            ['classname' => WidgetIconWebMeetingTeam::class]
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown() {
        $this->update(
            $this->tableName,
            ['status' => 1],
            ['classname' => WidgetIconWebMeetingTeam::class]
        );
    }
    
}
