<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Migrations
 */

use \open20\webmeeting\widgets\icons\WidgetIconWebMeetingTeam;

use yii\db\Migration;
use yii\rbac\Permission;
use mdm\admin\models\AuthItem;

/**
 * @inheritdoc
 */
class m201126_114508_permission_webmeeting_team_widgets extends Migration
{
    /**
     * @var type 
     */
    protected $authItemTableName;

    /**
     * @var type 
     */
    protected $widgetsList;
    
    /**
     * @inheritdoc
     */
    public function init() {
        $this->authItemTableName = '{{%auth_item}}';
        $this->widgetsList = [
            [
                'name' => WidgetIconWebMeetingTeam::class,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'VIEW perm on WidgetIconWebMeetingTeam widget'
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
            $this->insert($this->authItemTableName, $widget);
        }
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
                $this->authItemTableName, 
                ['name' => $widget['name']]
            );
        }
        
        $this->execute("SET foreign_key_checks = 1;");

        return true;
    }

}
