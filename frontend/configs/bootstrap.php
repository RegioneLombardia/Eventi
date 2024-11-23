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
//$bootstrap[] = 'frontend\bootstrap\BeforeRenderViewEvent';
$bootstrap[] = 'cms';
$bootstrap[] = 'translation';
$bootstrap[] = 'translatemanager';
$bootstrap[] = 'open20\amos\translation\bootstrap\EventActiveRecordBootstrap';
$bootstrap[] = 'admin';
$bootstrap[] = 'workflow';
$bootstrap[] = 'elasticsearch';
$bootstrap[] = 'backend\bootstrap\AddGoogleTagManager';





/*$bootstrap[] = [
    'class' => 'open20\amos\core\components\LanguageSelector',
    'supportedLanguages' => ['en-GB', 'it-IT'],
    'allowedIPs' => ['*']
];*/

if(!function_exists('pr')) {
    function pr($var, $info = '') {
        if(!defined('PR')) {
            define('PR', true);
        }
        if ($info) {
            $info = "<strong>$info: </strong>";
        }
        $debug = debug_backtrace(0);
        $result = "<pre style='font-size:11px;text-align:left;background:#fff;color:#000;'><strong>".$debug[0]['file'] ." ".$debug[0]['line']."</strong><br /> $info";
        $dump = print_r($var, true);
        $dump = highlight_string("<?php\n" . $dump, true);
        $dump = preg_replace('/&lt;\\?php<br \\/>/', '', $dump, 1);
        $result .= "$dump</pre>";

        echo $result;
    }
}

return $bootstrap;
