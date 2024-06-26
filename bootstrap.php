<?php
/**
 * CWP Media Load Flag Creator
 */

use Camoo\Config\Config;
use CWP\Core\Bootstrap;
use CWP\Core\EnvLoader;
use CWP\Core\Media;
use CWP\Core\MediaStopWatch;
use PHLAK\Stash;
use UTM\Utm;

/* Composer */
define('__COMPOSER_DIR__', __ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'vendor');
require __COMPOSER_DIR__.\DIRECTORY_SEPARATOR.'autoload.php';

define('__THIS_FILE__', basename($_SERVER['SCRIPT_FILENAME']));
define('__THIS_PAGE__', basename(__THIS_FILE__, '.php'));
define('__SCRIPT_NAME__', __THIS_PAGE__);

define('__CWP_SOURCE__', __ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'src');

define('__CWP_CONFIGURATION__', __CWP_SOURCE__.\DIRECTORY_SEPARATOR.'Configuration');
define('__PHP_YAML_DIR__', __CWP_CONFIGURATION__.'/Routes');
define('__ROUTE_NAV__', __PHP_YAML_DIR__.'/navigation.yaml');
define('__PAGE_CONFIG__', __PHP_YAML_DIR__.'/Pages.yaml');

define('__CWP_WEB_SOURCE__', __CWP_SOURCE__.\DIRECTORY_SEPARATOR.'Website');
define('__HTML_TEMPLATE__', __CWP_WEB_SOURCE__.'/Layout/Default');

define('__SQL_CONFIG_DIR__', __CWP_SOURCE__.\DIRECTORY_SEPARATOR.'Database');
define('__SQL_UPDATES_DIR__', __SQL_CONFIG_DIR__.\DIRECTORY_SEPARATOR.'updates');

define('__HTTP_ROOT__', __ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'www');
define('__PATH_ASSETS__', __HTTP_ROOT__.\DIRECTORY_SEPARATOR.'assets');
define('__LAYOUT_PATH__', __PATH_ASSETS__);

define('__CWP_VAR_DIR__', __ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'var');
define('__ERROR_LOG_DIRECTORY__', __CWP_VAR_DIR__.\DIRECTORY_SEPARATOR.'logs');
define('__CACHE_DIR__', __CWP_VAR_DIR__.\DIRECTORY_SEPARATOR.'cache');
define('__STASH_DIR__', __CACHE_DIR__.\DIRECTORY_SEPARATOR.'stash');
define('__TPL_CACHE_DIR__', __CACHE_DIR__.\DIRECTORY_SEPARATOR.'template');

// define('__CMD_ROOT__', __CWP_SOURCE__.\DIRECTORY_SEPARATOR.'Cmd');
// define('__CMD_CONFIG__', __CMD_ROOT__.\DIRECTORY_SEPARATOR.'Config');
Utm::LoadEnv();

$boot = new Bootstrap(new Config(__ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.'config.ini'));

define('__DATABASE_ROOT__', __ROOT_DIRECTORY__.\DIRECTORY_SEPARATOR.$boot->Config['db']['path']);
$boot->directory(__DATABASE_ROOT__);

Utm::$LOG_STYLE = 'pretty';
Utm::$LOG_DIR = __ERROR_LOG_DIRECTORY__.\DIRECTORY_SEPARATOR.__SCRIPT_NAME__;
new Utm();

Utm::$SHOW_HTML_DUMP = true;

register_shutdown_function('utmddump');
utminfo('---- START OF PAGE VIEW '.__SCRIPT_NAME__);

ini_set('max_execution_time', '600');
// register_shutdown_function([MediaStopWatch::class, 'flushLogs']);

$stash = Stash\Cache::file(function (): void {
    $this->setCacheDir(__STASH_DIR__);
});

Media::$Stash = $stash;
