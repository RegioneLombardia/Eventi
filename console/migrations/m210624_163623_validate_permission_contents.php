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
class m210624_163623_validate_permission_contents extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'VALIDATORE_NEWS',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'EVENT_DG_OPERATOR'
                    ]
                ]
            ],
            [
                'name' => 'VALIDATORE_DOCUMENTI',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'EVENT_DG_OPERATOR',
                    ]
                ]
            ],
            [
                'name' => 'VALIDATORE_DISCUSSIONI',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'EVENT_DG_OPERATOR'
                    ]
                ]
            ],
        ];

    }
}
