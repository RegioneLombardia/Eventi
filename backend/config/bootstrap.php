<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\basic\template
 * @category   CategoryName
 */

$bootstrap = [];

$bootstrap[] = 'translatemanager';
$bootstrap[] = 'translation';
$bootstrap[] = 'open20\amos\translation\bootstrap\EventActiveRecordBootstrap';
$bootstrap[] = 'backend\modules\eventsadmin\bootstrap\LanguageBeforeAction';
$bootstrap[] = [
    'class' => 'open20\amos\core\components\LanguageSelector',
    'supportedLanguages' => ['it-IT'],
//    'supportedLanguages' => ['en-GB', 'it-IT'],
    'allowedIPs' => ['*']
];

$bootstrap[] = 'open20\amos\core\bootstrap\Breadcrumb';
$bootstrap[] = 'workflow';
$bootstrap[] = 'comments';
$bootstrap[] = 'notify';
$bootstrap[] = 'mobilebridge';
$bootstrap[] = 'layout';
$bootstrap[] = 'seo';
$bootstrap[] = 'backend\modules\eventsadmin\bootstrap\RedirectBeforeAction';
$bootstrap[] = 'backend\modules\eventsadmin\bootstrap\AfterLogin';
$bootstrap[] = 'open20\amos\events\bootstrap\CompleteOperatorCourse';
$bootstrap[] = 'open20\amos\events\bootstrap\CommunityContentNotification';
$bootstrap[] = 'open20\amos\events\bootstrap\AfterInsertDocuments';
$bootstrap[] = 'elasticsearch';
$bootstrap[] = 'backend\bootstrap\AddGoogleTagManager';



return $bootstrap;
