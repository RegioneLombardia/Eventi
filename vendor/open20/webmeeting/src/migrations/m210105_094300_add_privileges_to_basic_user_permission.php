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

use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m200415_114300_assets_permissions
 */
class m210105_094300_add_privileges_to_basic_user_permission extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'USER_WEBMEETING',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Base permission of WEBMEETING',                
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'BASIC_USER'
                    ]
                ]
            ],
            [
                'name' => \open20\webmeeting\widgets\icons\WidgetIconWebMeetingDashboard::class,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Perm on WidgetIconWebMeetingDashboard widget',
                'ruleName' => null,
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'BASIC_USER'
                    ]
                ]
            ],
        ];
    }
    
}

