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


class m200331_192523_permission_role_to_super_user_event extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [

            [
                'name' => 'AMMINISTRATORE_USERIMPORTTASK',
                'update' => true,
                'newValues' => [
                    'addParents' => ['SUPER_USER_EVENT'],
                ]
            ],
            
        ];

    }
}
