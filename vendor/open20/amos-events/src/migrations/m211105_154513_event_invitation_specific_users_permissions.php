<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m211105_154513_event_invitation_specific_users_permissions*/
class m211105_154513_event_invitation_specific_users_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'EVENTINVITATIONSPECIFICUSERS_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model EventInvitationSpecificUsers',
                    'ruleName' => null,
                    'parent' => ['SUPER_USER_EVENT','EVENT_DG_OPERATOR','EVENTS_ADMINISTRATOR']
                ],
                [
                    'name' =>  'EVENTINVITATIONSPECIFICUSERS_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model EventInvitationSpecificUsers',
                    'ruleName' => null,
                    'parent' => ['SUPER_USER_EVENT','EVENT_DG_OPERATOR','EVENTS_ADMINISTRATOR']
                    ],
                [
                    'name' =>  'EVENTINVITATIONSPECIFICUSERS_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model EventInvitationSpecificUsers',
                    'ruleName' => null,
                    'parent' => ['SUPER_USER_EVENT','EVENT_DG_OPERATOR','EVENTS_ADMINISTRATOR']
                ],
                [
                    'name' =>  'EVENTINVITATIONSPECIFICUSERS_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model EventInvitationSpecificUsers',
                    'ruleName' => null,
                    'parent' => ['SUPER_USER_EVENT','EVENT_DG_OPERATOR','EVENTS_ADMINISTRATOR']
                ],

            ];
    }
}
