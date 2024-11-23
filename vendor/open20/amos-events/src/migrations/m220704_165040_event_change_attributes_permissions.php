<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m220704_165040_event_change_attributes_permissions*/
class m220704_165040_event_change_attributes_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'EVENTCHANGEATTRIBUTES_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model EventChangeAttributes',
                    'ruleName' => null,
                    'parent' => [ 'EVENTS_ADMINISTRATOR', 'SUPER_USER_EVENT']
                ],
                [
                    'name' =>  'EVENTCHANGEATTRIBUTES_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model EventChangeAttributes',
                    'ruleName' => null,
                    'parent' => [ 'EVENTS_ADMINISTRATOR', 'SUPER_USER_EVENT']
                    ],
                [
                    'name' =>  'EVENTCHANGEATTRIBUTES_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model EventChangeAttributes',
                    'ruleName' => null,
                    'parent' => [ 'EVENTS_ADMINISTRATOR', 'SUPER_USER_EVENT']
                ],
                [
                    'name' =>  'EVENTCHANGEATTRIBUTES_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model EventChangeAttributes',
                    'ruleName' => null,
                    'parent' => [ 'EVENTS_ADMINISTRATOR', 'SUPER_USER_EVENT']
                ],

            ];
    }
}
