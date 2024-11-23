<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m211209_152114_event_change_log_permissions*/
class m211209_152114_event_change_log_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'EVENTCHANGELOG_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model EventChangeLog',
                    'ruleName' => null,
                    'parent' => ['EVENTS_ADMINISTRATOR', 'SUPER_USER_EVENT']
                ],
                [
                    'name' =>  'EVENTCHANGELOG_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model EventChangeLog',
                    'ruleName' => null,
                    'parent' => ['EVENTS_ADMINISTRATOR', 'SUPER_USER_EVENT']
                    ],
                [
                    'name' =>  'EVENTCHANGELOG_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model EventChangeLog',
                    'ruleName' => null,
                    'parent' => ['EVENTS_ADMINISTRATOR', 'SUPER_USER_EVENT']
                ],
                [
                    'name' =>  'EVENTCHANGELOG_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model EventChangeLog',
                    'ruleName' => null,
                    'parent' => ['EVENTS_ADMINISTRATOR', 'SUPER_USER_EVENT']
                ],

            ];
    }
}
