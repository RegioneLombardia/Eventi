<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m230420_111116_event_landing_section_permissions*/
class m230420_111116_event_landing_section_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'EVENTLANDINGSECTION_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model EventLandingSection',
                    'ruleName' => null,
                    'parent' => ['EVENT_DG', 'EVENT_DG_OPERATOR','EVENTS_ADMINISTRATOR']
                ],
                [
                    'name' =>  'EVENTLANDINGSECTION_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model EventLandingSection',
                    'ruleName' => null,
                    'parent' => ['EVENT_DG', 'EVENT_DG_OPERATOR','EVENTS_ADMINISTRATOR']
                    ],
                [
                    'name' =>  'EVENTLANDINGSECTION_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model EventLandingSection',
                    'ruleName' => null,
                    'parent' => ['EVENT_DG', 'EVENT_DG_OPERATOR','EVENTS_ADMINISTRATOR']
                ],
                [
                    'name' =>  'EVENTLANDINGSECTION_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model EventLandingSection',
                    'ruleName' => null,
                    'parent' => ['EVENT_DG', 'EVENT_DG_OPERATOR','EVENTS_ADMINISTRATOR']
                ],

            ];
    }
}
