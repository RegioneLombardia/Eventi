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
class m220215_120023_permissions_community extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'COMMUNITY_CREATOR',
                'update' => true,
                'newValues' => [
                    'addParents' => [
                        'EVENT_DG_OPERATOR'
                    ],
                    'removeParents' => [
                        'VALIDATED_BASIC_USER'
                    ]
                ]
            ],
        ];

    }
}
