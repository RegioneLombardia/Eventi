<<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\webmeeting
 * @category   WebMeeting Migrations
 */

use open20\webmeeting\WebMeetingModule;

use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m200415_114300_assets_permissions
 */
class m210203_170707_webmeeting_history_permissions extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => open20\webmeeting\widgets\icons\WidgetIconWebMeetingMeetingHistory::class,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Perm for  WidgetIconWebMeetingMeetingHistory',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    WebMeetingModule::ADMIN_WEBMEETING
                ]
            ],
        ];
    }
    
}
