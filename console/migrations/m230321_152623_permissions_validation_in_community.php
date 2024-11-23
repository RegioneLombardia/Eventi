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
class m230321_152623_permissions_validation_in_community extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'EventValidate',
                'update' => true,
                'newValues' => [
                    'removeParents' => [
                        'VALIDATED_BASIC_USER'
                    ],
                    'addParents' => [
                        'EVENT_DG_OPERATOR'
                    ],
                ]
            ],

            [
                'name' => 'NewsValidate',
                'update' => true,
                'newValues' => [
                    'removeParents' => [
                        'VALIDATED_BASIC_USER'
                    ],
                    'addParents' => [
                        'EVENT_DG_OPERATOR'
                    ],
                ]
            ],

            [
                'name' => 'DocumentValidate',
                'update' => true,
                'newValues' => [
                    'removeParents' => [
                        'VALIDATED_BASIC_USER'
                    ],
                    'addParents' => [
                        'EVENT_DG_OPERATOR'
                    ],
                ]
            ],
            [
                'name' => 'DiscussionValidate',
                'update' => true,
                'newValues' => [
                    'removeParents' => [
                        'VALIDATED_BASIC_USER'
                    ],
                    'addParents' => [
                        'EVENT_DG_OPERATOR'
                    ],
                ]
            ],
        ];

    }
}
