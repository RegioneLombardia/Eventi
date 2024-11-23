<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m201020_121129_groups_comunication_permissions*/
class m201020_121129_groups_comunication_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'GROUPSCOMUNICATION_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model GroupsComunication',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_GROUPS']
                ],
                [
                    'name' =>  'GROUPSCOMUNICATION_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model GroupsComunication',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_GROUPS']
                    ],
                [
                    'name' =>  'GROUPSCOMUNICATION_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model GroupsComunication',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_GROUPS']
                ],
                [
                    'name' =>  'GROUPSCOMUNICATION_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model GroupsComunication',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_GROUPS']
                ],

            ];
    }
}
