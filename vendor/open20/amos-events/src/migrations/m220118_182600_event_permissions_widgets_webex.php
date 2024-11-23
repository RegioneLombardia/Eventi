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
class m220118_182600_event_permissions_widgets_webex extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [

            [
                'name' => \open20\amos\events\widgets\icons\WidgetWebexManagment::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Webex Managment',
                'parent' => ['EVENT_WEBEX_MANAGER']
            ],
        ];
    }
}
