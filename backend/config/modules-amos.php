<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

$modules = [
    'amministra-utenti' => [
        'class' => 'open20\amos\admin\RoleManager',
        'layout' => "@vendor/open20/amos-core/views/layouts/admin",
        //'left-menu', // it can be '@path/to/your/layout'.
        'controllerMap' => [
            'assignment' => [
                'class' => 'mdm\admin\controllers\AssignmentController',
                'userClassName' => 'common\models\User',
                'idField' => 'id'
            ],
            /*
              'other' => [
              'class' => 'path\to\OtherController', // add another controller
              ],
             */
        ],
        'menus' => [
            'assignment' => [
                'label' => 'Gestisci Assegnazioni' // TODO translate
            ],
        ]
    ],
   /**  'chat' => [
        'class' => 'open20\amos\chat\AmosChat',
    ], **/

    'dashboard' => [
        'class' => 'open20\amos\dashboard\AmosDashboard',
    ],
    'discussioni' => [
        'class' => 'open20\amos\discussioni\AmosDiscussioni',
    ],
    'groups' => [
        'class' => 'open20\amos\groups\Module',
    ],
    'layout' => [
        'class' => 'open20\amos\layout\Module',
    ],

    'monitor' => [
        'class' => 'backend\modules\monitor\Module',
    ],

    'myactivities' => [
        'class' => 'open20\amos\myactivities\AmosMyActivities',
    ],
    'privileges' => [
        'class' => 'open20\amos\privileges\AmosPrivileges',
    ],

    'tag' => [
        'class' => 'open20\amos\tag\AmosTag',
    ],

    'upload' => [
        'class' => 'open20\amos\upload\AmosUpload',
    ],
    'utility' => [
        'class' => 'open20\amos\utility\Module'
    ],
    'report' => [
        'class' => 'open20\amos\report\AmosReport',
        'modelsEnabled' => [
            'open20\amos\discussioni\models\DiscussioniTopic',
            'open20\amos\documenti\models\Documenti',
            'open20\amos\events\models\Event',
            'open20\amos\news\models\News',
        ]
    ],
    'rss' => [
        'class' => 'amos\rss\Module',
        'modelsEnabled' => [
            'open20\amos\events\models\Event',
//            'open20\amos\news\models\News',
//            'open20\amos\documenti\models\Documenti',
//            'open20\amos\discussioni\models\DiscussioniTopic',
//            'open20\amos\community\models\Community',
        ],
    ],
    'sondaggi' => [
        'class' => 'open20\amos\sondaggi\AmosSondaggi',
    ],
    'sitemanagement' => [
        'class' => 'amos\sitemanagement\Module',
        'enableUploadVideoSlider' => false,
        'enableTextSlider' => false
    ],
    'workflow' => [
        'class' => 'open20\amos\workflow\AmosWorkflow',
    ],

];


return $modules;
