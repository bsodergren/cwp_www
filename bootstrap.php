<?php

use CWP\Core\Media;
use CWP\Core\Bootstrap;
use Camoo\Config\Config;
use CWP\Core\MediaStopWatch;
use PHLAK\Stash;

define('__COMPOSER_DIR__', __PUBLIC_ROOT__ . \DIRECTORY_SEPARATOR . 'vendor');
define('__CWP_SOURCE__', __PUBLIC_ROOT__ . \DIRECTORY_SEPARATOR . 'src');
define('__CONFIG_ROOT__', __CWP_SOURCE__ . \DIRECTORY_SEPARATOR . 'Configuration');
define('__ERROR_LOG_DIRECTORY__', __CWP_SOURCE__ . \DIRECTORY_SEPARATOR . 'var' . \DIRECTORY_SEPARATOR . 'logs');
define('__CACHE_DIR__', __CWP_SOURCE__ . \DIRECTORY_SEPARATOR . 'var' . \DIRECTORY_SEPARATOR . 'cache');
define('__STASH_DIR__', __CACHE_DIR__ . \DIRECTORY_SEPARATOR . 'stash' . \DIRECTORY_SEPARATOR);

define('__TPL_CACHE_DIR__', __CACHE_DIR__ . \DIRECTORY_SEPARATOR . 'template' . \DIRECTORY_SEPARATOR);
define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));

require __COMPOSER_DIR__ . \DIRECTORY_SEPARATOR . 'autoload.php';
$boot =  new Bootstrap(new Config(__PUBLIC_ROOT__ . \DIRECTORY_SEPARATOR . 'config.ini'));

ini_set('max_execution_time', '600');
register_shutdown_function([MediaStopWatch::class, 'flushLogs']);

$stash = Stash\Cache::file(function (): void {
    $this->setCacheDir(__STASH_DIR__);

});

Media::$Stash = $stash;
