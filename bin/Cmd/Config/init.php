<?php

use CWP\Core\MediaStopWatch;



$boot->definePath('__DATABASE_ROOT__', $boot->Config['db']['path'] . \DIRECTORY_SEPARATOR . 'database');
$boot->directory(__DATABASE_ROOT__);

$boot->definePath('__SQL_CONFIG_DIR__', __CWP_SOURCE__ . \DIRECTORY_SEPARATOR . 'Database');
$boot->definePath('__SQL_UPDATES_DIR__', __SQL_CONFIG_DIR__ . \DIRECTORY_SEPARATOR . 'updates');


$boot->getDatabase();
define('__APP_CACHE_DIR__',__CACHE_DIR__);

define('__HTTP_ROOT__', $boot->Config['server']['http_root'].$boot->Config['server']['url_root']);

//define('__HTTP_ROOT__',false);
define('__TEMP_DIR__', sys_get_temp_dir());

require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'path_constants.php';
require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'boot.php';
require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'settings.php';
// require_once __CONFIG_ROOT__ . \DIRECTORY_SEPARATOR . 'init.php';
