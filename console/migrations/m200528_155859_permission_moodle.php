<?php
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m200402_182259_event_communication_permissions*/
class m200528_155859_permission_moodle extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = '';

        return [
            [
                'name' => 'CANDIDATO_OPERATORE',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Candidato operatore'
            ],
            [
                'name' => 'MOODLE_STUDENT',
                'update' => true,
                'newValues' => [
                    'addParents' => ['CANDIDATO_OPERATORE'],
                ]
            ],


        ];
    }
}
