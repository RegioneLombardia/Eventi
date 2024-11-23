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
class m210625_163623_validate_permission_contents_2 extends AmosMigrationPermissions
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
                        'SUPER_USER_EVENT'
                    ]
                ]
            ],
            [
                'name' => 'VALIDATORE_DOCUMENTI',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'SUPER_USER_EVENT',
                    ]
                ]
            ],
            [
                'name' => 'VALIDATORE_DISCUSSIONI',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'SUPER_USER_EVENT'
                    ]
                ]
            ],
            // ------------------
            [
                'name' => 'CREATORE_NEWS',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'SUPER_USER_EVENT'
                    ]
                ]
            ],
            [
                'name' => 'CREATORE_DOCUMENTI',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'SUPER_USER_EVENT',
                    ]
                ]
            ],
            [
                'name' => 'CREATORE_DISCUSSIONI',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'SUPER_USER_EVENT'
                    ]
                ]
            ],
        ];

    }
}
