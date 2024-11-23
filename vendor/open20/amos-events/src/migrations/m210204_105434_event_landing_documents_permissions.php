<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
 * Class m210204_105434_event_landing_documents_permissions*/
class m210204_105434_event_landing_documents_permissions extends AmosMigrationPermissions
{

    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
            [
                'name' =>  'EVENTLANDINGDOCUMENTS_CREATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di CREATE sul model EventLandingDocuments',
                'ruleName' => null,
                'parent' => ['EVENT_DG_OPERATOR', 'EVENTS_ADMINISTRATOR', 'SUPER_USER_EVENT']
            ],
            [
                'name' =>  'EVENTLANDINGDOCUMENTS_READ',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di READ sul model EventLandingDocuments',
                'ruleName' => null,
                'parent' => ['EVENT_DG_OPERATOR', 'EVENTS_ADMINISTRATOR','SUPER_USER_EVENT']
            ],
            [
                'name' =>  'EVENTLANDINGDOCUMENTS_UPDATE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di UPDATE sul model EventLandingDocuments',
                'ruleName' => null,
                'parent' => ['EVENT_DG_OPERATOR', 'EVENTS_ADMINISTRATOR','SUPER_USER_EVENT']
            ],
            [
                'name' =>  'EVENTLANDINGDOCUMENTS_DELETE',
                'type' => Permission::TYPE_PERMISSION,
                'description' => 'Permesso di DELETE sul model EventLandingDocuments',
                'ruleName' => null,
                'parent' => ['EVENT_DG_OPERATOR', 'EVENTS_ADMINISTRATOR', 'SUPER_USER_EVENT']
            ],

        ];
    }
}
