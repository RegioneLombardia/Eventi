<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m171214_121807_add_auth_item_groups*/
class m171214_121807_add_auth_item_groups extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = 'Permissions for the dashboard for the widget ';

        return [
                [
                    'name' =>  \open20\amos\groups\widgets\icons\WidgetIconGroups::className(),
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => $prefixStr . 'WidgetIconGroups',
                    'ruleName' => null,
                    'parent' => ['AMMINISTRATORE_GROUPS']
                ]

            ];
    }
}
