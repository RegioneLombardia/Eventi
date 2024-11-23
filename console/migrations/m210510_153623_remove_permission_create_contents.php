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
class m210510_153623_remove_permission_create_contents extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'CREATORE_NEWS',
                'update' => true,
                'newValues' => [
                    'removeParents' => [
                        'BASIC_USER',
                        'VALIDATED_BASIC_USER'

                    ],
                    'addParents' => [
                        'EVENT_DG_OPERATOR'
                    ]
                ]
            ],
            [
                'name' => 'CREATORE_DOCUMENTI',
                'update' => true,
                'newValues' => [
                    'removeParents' => [
                        'BASIC_USER',
                        'VALIDATED_BASIC_USER'
                    ],
                    'addParents' => [
                        'EVENT_DG_OPERATOR',
                        'VALIDATED_BASIC_USER'

                    ]
                ]
            ],
            [
                'name' => 'CREATORE_DISCUSSIONI',
                'update' => true,
                'newValues' => [
                    'removeParents' => [
                        'BASIC_USER',
                        'VALIDATED_BASIC_USER'

                    ],
                    'addParents' => [
                        'EVENT_DG_OPERATOR'
                    ]
                ]
            ],
        ];

    }
}
