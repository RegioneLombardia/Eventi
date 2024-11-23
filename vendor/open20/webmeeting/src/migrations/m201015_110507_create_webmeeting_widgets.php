<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Migrations
 */

use open20\webmeeting\WebMeetingModule;
use open20\webmeeting\widgets\icons\WidgetIconWebMeetingDashboard;

use open20\amos\dashboard\models\AmosWidgets;

use yii\db\Migration;

/**
 * @inheritdoc
 */
class m201015_110507_create_webmeeting_widgets extends Migration
{
    /**
     * @var type
     */
    protected $moduleName;

    /**
     * @var type
     */
    protected $amosWidgetTableName;

    /**
     * @var type 
     */
    protected $widgetsList;

    /**
     * @inheritdoc
     */
    public function init() {
        $this->amosWidgetTableName = AmosWidgets::tableName();
        $this->moduleName = WebMeetingModule::getModuleName();
        $this->widgetsList = [
            [
                'classname' => WidgetIconWebMeetingDashboard::class,
                'type' => AmosWidgets::TYPE_ICON,
                'module' => $this->moduleName,
                'status' => AmosWidgets::STATUS_ENABLED,
                'default_order' => 666,
                'dashboard_visible' => 1,
            ],
        ];
    }

    /**
     * Insert widgets into amos widgets table
     * 
     * @inheritdoc
     */
    public function safeUp() {
        foreach($this->widgetsList as $widget) {
            if (!($this->checkWidgetExist($widget['classname']))) {
                $this->insert($this->amosWidgetTableName, $widget);
            }
        }
    }
    
    /**
     * Check if it is already installed
     * 
     * @param type $classname
     * @return type
     */
    private function checkWidgetExist($classname) {
        return AmosWidgets::find()
            ->andWhere(['classname' => $classname])
            ->count();
    }

    /**
     * Remove widgets
     * 
     * @inheritdoc
     */
    public function safeDown() {
        $this->execute("SET foreign_key_checks = 0;");
        
        foreach ($this->widgetsList as $widget) {
            $this->delete(
                $this->amosWidgetTableName,
                ['classname' => $widget['classname']]
            );
        }
        
        $this->execute("SET foreign_key_checks = 1;");

        return true;
    }

}
