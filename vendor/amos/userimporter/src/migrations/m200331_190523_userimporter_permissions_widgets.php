<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\migrations
 * @category   CategoryName
 */

use amos\userimporter\widgets\icons\WidgetIconImporter;
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


class m200331_190523_userimporter_permissions_widgets extends AmosMigrationPermissions
{

    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => WidgetIconImporter::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'User Importer',
                'parent' => ['AMMINISTRATORE_USERIMPORTTASK']
            ],
        ];
    }
}