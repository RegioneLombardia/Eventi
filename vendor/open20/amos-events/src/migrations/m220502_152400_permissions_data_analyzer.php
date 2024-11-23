<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community\migrations
 * @category   CategoryName
 */

use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;

/**
 * Class m191003_121523_event_seats_permissions*/
class m220502_152400_permissions_data_analyzer extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'EVENT_DATA_ANALYZER',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Ruolo Amministrore analisi dati',
                'parent' => ['ADMIN']
            ],
            [
                'name' => \open20\amos\events\widgets\icons\WidgetIconEventDataAnalyzer::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permeso widget data analyzer',
                'parent' => ['EVENT_DATA_ANALYZER']
            ],
        ];
    }
}
