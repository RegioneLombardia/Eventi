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
class m221102_121200_event_permissions_widgets_marketing_automation extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [

            [
                'name' => \open20\amos\events\widgets\icons\WidgetIconMarketingAutomation::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Marketing automation',
                'parent' => ['EVENT_DG_OPERATOR', 'EVENTS_ADMINISTRATOR','SUPER_USER_EVENT']
            ],
        ];
    }
}
