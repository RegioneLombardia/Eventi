<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m220804_163731_user_import_edit_list_permissions*/
class m220804_163731_user_import_edit_list_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'USERIMPORTEDITLIST_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model UserImportEditList',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_USERIMPORTTASK']
                ],
                [
                    'name' =>  'USERIMPORTEDITLIST_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model UserImportEditList',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_USERIMPORTTASK']
                    ],
                [
                    'name' =>  'USERIMPORTEDITLIST_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model UserImportEditList',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_USERIMPORTTASK']
                ],
                [
                    'name' =>  'USERIMPORTEDITLIST_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model UserImportEditList',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_USERIMPORTTASK']
                ],

            ];
    }
}
