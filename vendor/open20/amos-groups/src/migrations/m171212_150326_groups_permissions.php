<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m171212_150326_groups_permissions*/
class m171212_150326_groups_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
            [
                'name' =>  'AMMINISTRATORE_GROUPS',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Permission for Administrator Groups',
                'ruleName' => null,
                'parent' => ['ADMIN']
            ],
            [
                'name' =>  'GROUPS_CREATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di CREATE sul model Groups',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_GROUPS']
            ],
            [
                'name' =>  'GROUPS_READ',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di READ sul model Groups',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_GROUPS']
            ],
            [
                'name' =>  'GROUPS_UPDATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di UPDATE sul model Groups',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_GROUPS']
            ],
            [
                'name' =>  'GROUPS_DELETE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di DELETE sul model Groups',
                'ruleName' => null,
                'parent' => ['AMMINISTRATORE_GROUPS']
            ],

        ];
    }
}
