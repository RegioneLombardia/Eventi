<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m171212_150353_groups_members_permissions*/
class m171212_150353_groups_members_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'GROUPSMEMBERS_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model GroupsMembers',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_GROUPS']
                ],
                [
                    'name' =>  'GROUPSMEMBERS_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model GroupsMembers',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_GROUPS']
                    ],
                [
                    'name' =>  'GROUPSMEMBERS_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model GroupsMembers',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_GROUPS']
                ],
                [
                    'name' =>  'GROUPSMEMBERS_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model GroupsMembers',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_GROUPS']
                ],

            ];
    }
}
