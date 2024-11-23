<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    ebike\assets
 * @category   CategoryName
 */

use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
 * Class m200415_114300_assets_permissions
 */
class m210703_134324_add_admin_webmeeting_to_community_manager extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => \open20\webmeeting\WebMeetingModule::ADMIN_WEBMEETING,
                'type' => Permission::TYPE_ROLE,
                'update' => true,
                'newValues' => [
                    'addParents' => ['AMMINISTRATORE_COMMUNITY']
                ]
            ],
        ];
    }
}