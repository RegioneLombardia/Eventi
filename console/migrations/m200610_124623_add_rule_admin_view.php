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
class m200610_124623_add_rule_admin_view extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [

            [
                'name' => 'USERPROFILE_READ',
                'update' => true,
                'newValues' => [
                    'addParents' => ['UpdateOwnUserProfile'],
                    'removeParents' => ['BASIC_USER']
                ]
            ],
        ];

    }
}
