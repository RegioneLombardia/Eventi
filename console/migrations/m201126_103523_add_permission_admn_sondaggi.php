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
class m201126_103523_add_permission_admn_sondaggi extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [

            [
                'name' => 'AMMINISTRAZIONE_SONDAGGI',
                'update' => true,
                'newValues' => [
                    'addParents' => ['SUPER_USER_EVENT','EVENT_DG_OPERATOR'],
                ]
            ],


        ];

    }
}
