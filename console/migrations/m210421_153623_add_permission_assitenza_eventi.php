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
class m210421_153623_add_permission_assitenza_eventi extends AmosMigrationPermissions
{
    /**
     * @inheritdoc
     */
    protected function setRBACConfigurations()
    {
        return [
            [
                'name' => 'ASSISTENZA_EVENTI',
                'type' => Permission::TYPE_ROLE,
                'description' => 'Assisitenza eventi',
                'children' => [
                    'SUPER_USER',
                    'AMMINISTRATORE_DISCUSSIONI',
                    'AMMINISTRATORE_DOCUMENTI',
                    'AMMINISTRATORE_NEWS',
                    'AMMINISTRATORE_EMAIL_MANAGER',
                    'AMMINISTRATORE_GROUPS',
                    'AMMINISTRATORE_UTENTI',
                    'AMMINISTRAZIONE_SONDAGGI',
                    'EVENTS_ADMINISTRATOR',
                    'COMMENTS_ADMINISTRATOR',
                    'CONTENT_TRANSLATOR',
                    'TRANSLATE_MANAGER',
                    'TRANSLATION_ADMINISTRATOR',
                    'INVITATIONS_ADMINISTRATOR',
                    'IMPERSONATE_USERS',
                    'open20\amos\dashboard\widgets\icons\WidgetIconManagement',
                    'open20\amos\emailmanager\widgets\icons\WidgetIconEmailManager',
                    'open20\amos\emailmanager\widgets\icons\WidgetIconSpool',
                    'open20\amos\emailmanager\widgets\icons\WidgetIconTemplate',
                    'open20\amos\events\widgets\icons\WidgetIconLocation',
                    'amos\cmsbridge\widgets\icons\WidgetIconCmsDashboard',
                    'EMAILVIEW_DELETE',
                    'EMAILVIEW_READ',
                    'EMAILVIEW_UPDATE',
                    'GESTIONE_UTENTI',


                ]
            ],
        ];

    }
}
