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
class m201015_110000_webmeeting_roles_permissions extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => WebMeetingModule::ADMIN_WEBMEETING,
                'type' => Permission::TYPE_ROLE,
                'description' => 'ADMIN_WEBMEETING with admin perms',
                'ruleName' => null,
                'parent' => ['ADMIN']
            ],
            [
                'name' => WebMeetingModule::USER_WEBMEETING,
                'type' => Permission::TYPE_ROLE,
                'description' => 'USER_WEBMEETING role',
                'ruleName' => null,
            ],
            [
                'name' => WebMeetingModule::WEBMEETING_MODEL_CREATE,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'CREATE perm on WEBMEETING model',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    WebMeetingModule::ADMIN_WEBMEETING
                ]
            ],
            [
                'name' => WebMeetingModule::WEBMEETING_MODEL_READ,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'READ perm on WEBMEETING model',
                'ruleName' => null,
                'parent' => [
                    'ADMIN', 
                    WebMeetingModule::ADMIN_WEBMEETING,
                    WebMeetingModule::USER_WEBMEETING
                ]
            ],
            [
                'name' => WebMeetingModule::WEBMEETING_MODEL_UPDATE,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'UPDATE perm on WEBMEETING model',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    WebMeetingModule::ADMIN_WEBMEETING
                ]
            ],
            [
                'name' => WebMeetingModule::WEBMEETING_MODEL_DELETE,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'DELETE perm on WEBMEETING model',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    WebMeetingModule::ADMIN_WEBMEETING
                ]
            ],
        ];
    }
    
}
