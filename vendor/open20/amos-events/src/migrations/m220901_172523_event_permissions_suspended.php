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
class m220901_172523_event_permissions_suspended extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [

//-------------------------------------------- WORKFLOW
            [
                'name' => \open20\amos\events\models\Event::EVENTS_WORKFLOW_STATUS_SUSPENDED,
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso workflow annulla evento',
                'ruleName' => null,
                'parent' => ['EVENTS_ADMINISTRATOR', 'EVENT_DG_OPERATOR', 'SUPER_USER_EVENT']
            ]
            ];
    }
}
