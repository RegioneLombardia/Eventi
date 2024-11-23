<?php
//return require_once( './frontend/configs/env.php');

chdir(__DIR__.'/../frontend');

$finalConfig = require_once( './configs/env.php');

unset($finalConfig['components']['request']);
unset($finalConfig['components']['translatemanager']);
unset($finalConfig['components']['session']);
unset($finalConfig['components']['errorHandler']);
unset($finalConfig['modules']['mobilebridge']);
unset($finalConfig['modules']['favorites']);
unset($finalConfig['bootstrap']['favorites']);



return $finalConfig;

