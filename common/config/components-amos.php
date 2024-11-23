<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

return [
    'formatter' => [
        'class' => 'open20\amos\core\formatter\Formatter',
        'dateFormat' => 'php:d/m/Y',
        'datetimeFormat' => 'php:d/m/Y H:i',
        'timeFormat' => 'php:H:i',
        'defaultTimeZone' => 'Europe/Rome',
        'timeZone' => 'Europe/Rome',
        'locale' => 'it-IT',
        'thousandSeparator' => '.',
        'decimalSeparator' => ',',
    ],
    'imageUtility' => [
        'class' => 'open20\amos\core\components\ImageUtility',
    ],
    'view' => [
        'class' => 'open20\amos\core\components\AmosView',
        'theme' => [
            'pathMap' => [
                '@vendor/open20/amos-admin/src/views/user-profile' => '@app/modules/eventsadmin/views/user-profile',
                '@vendor/open20/amos-admin/src/views/security' => '@app/modules/eventsadmin/views/security',
            ],
        ],
    ],
    'workflowSource' => [
        'class' => 'open20\amos\core\workflow\ContentDefaultWorkflowDbSource',
    ],
];
