<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m220726_125840_event_related_permissions*/
class m220726_125840_event_related_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'EVENTRELATED_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model EventRelated',
                    'ruleName' => null,
                    'parent' => ['EVENTS_ADMINISTRATOR','EVENT_DG_OPERATOR','SUPER_USER_EVENT']
                ],
                [
                    'name' =>  'EVENTRELATED_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model EventRelated',
                    'ruleName' => null,
                    'parent' => ['EVENTS_ADMINISTRATOR','EVENT_DG_OPERATOR','SUPER_USER_EVENT']
                    ],
                [
                    'name' =>  'EVENTRELATED_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model EventRelated',
                    'ruleName' => null,
                    'parent' => ['EVENTS_ADMINISTRATOR','EVENT_DG_OPERATOR','SUPER_USER_EVENT']
                ],
                [
                    'name' =>  'EVENTRELATED_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model EventRelated',
                    'ruleName' => null,
                    'parent' => ['EVENTS_ADMINISTRATOR','EVENT_DG_OPERATOR','SUPER_USER_EVENT']
                ],

            ];
    }
}
