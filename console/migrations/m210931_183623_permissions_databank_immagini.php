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
class m210931_183623_permissions_databank_immagini extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'MANAGE_ATTACH_GALLERY',
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
