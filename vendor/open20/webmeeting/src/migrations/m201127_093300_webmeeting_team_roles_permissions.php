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
class m201127_093300_webmeeting_team_roles_permissions extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => WebMeetingModule::WEBMEETING_TEAM_MODEL_CREATE,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'CREATE perm on WebMeeting Team model',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    WebMeetingModule::ADMIN_WEBMEETING
                ]
            ],
            [
                'name' => WebMeetingModule::WEBMEETING_TEAM_MODEL_READ,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'READ perm on WebMeeting Team model',
                'ruleName' => null,
                'parent' => [
                    'ADMIN', 
                    WebMeetingModule::ADMIN_WEBMEETING,
                    WebMeetingModule::USER_WEBMEETING
                ]
            ],
            [
                'name' => WebMeetingModule::WEBMEETING_TEAM_MODEL_UPDATE,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'UPDATE perm on WebMeeting Team model',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    WebMeetingModule::ADMIN_WEBMEETING
                ]
            ],
            [
                'name' =>  WebMeetingModule::WEBMEETING_TEAM_MODEL_DELETE,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'DELETE perm on WebMeeting Team model',
                'ruleName' => null,
                'parent' => [
                    'ADMIN',
                    WebMeetingModule::ADMIN_WEBMEETING
                ]
            ],
        ];
    }
    
}
