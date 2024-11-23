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
class m211021_111323_permissions_databank_immagini_operator extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'ATTACH_GALLERY_OPERATOR',
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
