<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
 * Class m190527_191336_event_accreditation_list_permissions*/
class m220118_170000_event_webex_list_permissions extends AmosMigrationPermissions
{

    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {

        return [
            [
                'name' =>  'EVENT_WEBEX_MANAGER',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Permesso di gestione delle configurazioni per WEBEX',
                'ruleName' => null,
                'parent' => ['ADMIN','EVENTS_ADMINISTRATOR']
            ],
            [
                'name' =>  'EVENT_WEBEX_CREATOR',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Permesso di creazione eventi di tipologia WEBEX',
                'ruleName' => null,
                'parent' => null,
            ],

        ];
    }
}
