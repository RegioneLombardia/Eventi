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

$bootstrap[] = 'log';
$bootstrap[] = 'translation';
$bootstrap[] = 'translatemanager';
$bootstrap[] = 'open20\amos\translation\bootstrap\EventActiveRecordBootstrap';
$bootstrap[] = 'notify';

return $bootstrap;
