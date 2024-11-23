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

use open20\amos\core\migration\AmosMigration;

use yii\db\Migration;
use yii\rbac\Permission;

/**
 * @inheritdoc
 */
class m201015_111008_permission_webmeeting_dashboard_widget extends AmosMigration
{
    /**
     * @var type 
     */
    protected $authorizations;
    
    /**
     * @inheritdoc
     */
    public function init() {
        $this->authorizations = [
            [
                'name' => WidgetIconWebMeetingDashboard::class,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Perm on WidgetIconWebMeetingDashboard widget',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    WebMeetingModule::ADMIN_WEBMEETING,
                    WebMeetingModule::USER_WEBMEETING
                ]
            ],
        ];
    }

    /**
     * Insert widgets into amos widgets table
     * 
     * @inheritdoc
     */
    public function safeUp() {
        return $this->addAuthorizations();
    }
    
    /**
     * Remove widgets
     * 
     * @inheritdoc
     */
    public function safeDown() {
        return $this->removeAuthorizations();
    }

}
