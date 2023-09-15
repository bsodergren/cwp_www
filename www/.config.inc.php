<?php
/**
 * CWP Media tool.
 */

use Camoo\Config\Config;
use CWP\Core\Bootstrap;
use Tracy\Debugger;

define('__PROJECT_ROOT__', dirname(__FILE__, 3));
define('__PUBLIC_ROOT__', dirname(__FILE__, 2));
define('__HTTP_ROOT__', dirname(__FILE__, 1));

define('__COMPOSER_DIR__', __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'vendor');
define('__CWP_SOURCE__', __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'src');
define('__CONFIG_ROOT__', __CWP_SOURCE__.\DIRECTORY_SEPARATOR.'Configuration');

//require_once __CONFIG_ROOT__.\DIRECTORY_SEPARATOR.'composer.php';

// set_include_path(get_include_path().PATH_SEPARATOR.__COMPOSER_DIR__);
require __COMPOSER_DIR__.\DIRECTORY_SEPARATOR.'autoload.php';

 //Debugger::enable();

// // Debugger::$showLocation = Tracy\Dumper::LOCATION_SOURCE; // Shows path to where the dump() was called
// Debugger::$logSeverity = \E_WARNING | \E_NOTICE;
// Debugger::$dumpTheme = 'dark';
// Debugger::$showBar = true;          // (bool) defaults to true
// // Debugger::$strictMode = ~\E_DEPRECATED & ~\E_USER_DEPRECATED & ~\E_NOTICE;

// Debugger::$showLocation = Tracy\Dumper::LOCATION_CLASS | Tracy\Dumper::LOCATION_LINK; // Shows both paths to the classes and link to where the dump() was called
// // Debugger::$showLocation = false; // Hides additional location information
// // Debugger::$showLocation = true; // Shows all additional location information

$boot = new Bootstrap(new Config(__PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'config.ini'));

//$boot->definePath('__DATABASE_ROOT__', dirname(__FILE__, 2).\DIRECTORY_SEPARATOR.'database');

$boot->definePath('__DATABASE_ROOT__', $boot->Config['db']['path'].\DIRECTORY_SEPARATOR.'database');
$boot->directory(__DATABASE_ROOT__);

$boot->definePath('__SQL_CONFIG_DIR__', __CWP_SOURCE__.\DIRECTORY_SEPARATOR.'Database');
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


$req_file = $_SERVER['REQUEST_URI'];
$req =  '?'.$_SERVER['QUERY_STRING'];
$req_file = str_replace(__URL_PATH__ .'/','',$req_file);
if($req_file == "")
{
    header("Location:  ".__URL_PATH__ . "/index.php");
    exit();
}
