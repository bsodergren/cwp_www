<?php
/**
 * CWP Media tool
 */

use Camoo\Config\Config;
use CWP\Bootstrap;
use Tracy\Debugger;

define('__PROJECT_ROOT__', dirname(__FILE__, 3));
define('__PUBLIC_ROOT__', dirname(__FILE__, 2));
define('__HTTP_ROOT__', dirname(__FILE__, 1));
define('__CONFIG_ROOT__', dirname(__FILE__, 2).\DIRECTORY_SEPARATOR.'config');
define('__COMPOSER_DIR__', __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'vendor');

// set_include_path(get_include_path().PATH_SEPARATOR.__COMPOSER_DIR__);
require __COMPOSER_DIR__.\DIRECTORY_SEPARATOR.'autoload.php';

// Debugger::enable();

$boot = new Bootstrap(new Config(__PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'config.ini'));
$boot->definePath('__DATABASE_ROOT__', dirname(__FILE__, 2).\DIRECTORY_SEPARATOR.'database');
$boot->directory(__DATABASE_ROOT__);
$boot->definePath('__SQL_CONFIG_DIR__', __CONFIG_ROOT__.\DIRECTORY_SEPARATOR.'configuration');
$boot->definePath('__SQL_UPDATES_DIR__', __SQL_CONFIG_DIR__.\DIRECTORY_SEPARATOR.'updates');

$boot->definePath('__ASSETS_DIR__', __HTTP_ROOT__.\DIRECTORY_SEPARATOR.'assets');
$boot->definePath('__INC_CORE_DIR__', __ASSETS_DIR__.\DIRECTORY_SEPARATOR.'core');

$boot->getDatabase();

define('__TEMP_DIR__', sys_get_temp_dir());

require_once __CONFIG_ROOT__.\DIRECTORY_SEPARATOR.'path_constants.php';
require_once __CONFIG_ROOT__.\DIRECTORY_SEPARATOR.'url_paths.php';
require_once __CONFIG_ROOT__.\DIRECTORY_SEPARATOR.'variables.php';
require_once __CONFIG_ROOT__.\DIRECTORY_SEPARATOR.'boot.php';
require_once __CONFIG_ROOT__.\DIRECTORY_SEPARATOR.'settings.php';
require_once __CONFIG_ROOT__.\DIRECTORY_SEPARATOR.'init.php';
