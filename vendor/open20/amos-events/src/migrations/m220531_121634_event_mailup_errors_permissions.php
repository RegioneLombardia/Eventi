<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m220531_121634_event_mailup_errors_permissions*/
class m220531_121634_event_mailup_errors_permissions extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
                [
                    'name' =>  'EVENTMAILUPERRORS_CREATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di CREATE sul model EventMailupErrors',
                    'ruleName' => null,
                    'parent' => ['EVENT_DATA_ANALYZER']
                ],
                [
                    'name' =>  'EVENTMAILUPERRORS_READ',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di READ sul model EventMailupErrors',
                    'ruleName' => null,
                    'parent' => ['EVENT_DATA_ANALYZER']
                    ],
                [
                    'name' =>  'EVENTMAILUPERRORS_UPDATE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di UPDATE sul model EventMailupErrors',
                    'ruleName' => null,
                    'parent' => ['EVENT_DATA_ANALYZER']
                ],
                [
                    'name' =>  'EVENTMAILUPERRORS_DELETE',
                    'type' => Permission::TYPE_PERMISSION,
                    'description' => 'Permesso di DELETE sul model EventMailupErrors',
                    'ruleName' => null,
                    'parent' => ['EVENT_DATA_ANALYZER']
                ],

            ];
    }
}
