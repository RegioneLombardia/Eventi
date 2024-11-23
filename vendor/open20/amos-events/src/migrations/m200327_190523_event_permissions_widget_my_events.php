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
class m200327_190523_event_permissions_widget_my_events extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [

            [
                'name' => \open20\amos\events\widgets\icons\WidgetIconMyEvents::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'My events',
                'parent' => ['EVENTS_ADMINISTRATOR', 'EVENTS_READER']
            ],
        ];
    }
}
