<?php
if(file_exists(".config/configure.php"))
{
//    header("Location:  .config/configure.php ");
 //   exit; 
}

function debug(...$var)
{
    echo "<pre>" . var_export($var, 1) . "</pre>";
}

define('__COMPOSER_DIR__', __DIR__ . '/library/vendor');
set_include_path(get_include_path() . PATH_SEPARATOR . __COMPOSER_DIR__);
require __COMPOSER_DIR__ . '/autoload.php';

use Noodlehaus\Config;
use Noodlehaus\Parser\ini;
use Nette\Utils\FileSystem;

$config_file = $_SERVER['DOCUMENT_ROOT']."/.config/config.ini";

$conf = new Config($config_file);

/**
 *  Basic constants for application that are displayed in the output
 */

define('APP_NAME', $conf['application']['name']);
define('APP_ORGANIZATION', 'cwp');
define('APP_OWNER', 'bjorn');
define('APP_DESCRIPTION', 'Embeddable PHP Login System');

/*
 * base directory and script name.
 */


define('__APP_INSTALL_DIR__', rtrim($conf['server']['url_root'], '/'));
define('__WEB_ROOT__',      FileSystem::normalizePath($conf['server']['web_root'] . __APP_INSTALL_DIR__));
define('__PROJECT_ROOT__',  FileSystem::normalizePath($conf['server']['root_dir']));
define('__ROOT_BIN_DIR__',  FileSystem::normalizePath($conf['server']['bin_dir']));
define('__SQLITE_DIR__',    FileSystem::normalizePath($conf['server']['db_dir']));
define('__URL_PATH__', __APP_INSTALL_DIR__);


list($__filename) = explode("?", $_SERVER['REQUEST_URI']);
$__request_name = basename($__filename,'.php');
$__script_name = basename($_SERVER['SCRIPT_NAME'], '.php');

if (  $__request_name != $__script_name )
{
 //   echo " $__request_name and $__script_name  dont match";

    
    header("Location:  ".__URL_PATH__ . "/index.php");
    exit;
}


define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));

/*
 * Default constants for include path structure.
 *
 */
define('__ASSETS_DIR__', __WEB_ROOT__ . '/assets');
define('__INC_CLASS_DIR__', __ASSETS_DIR__ . '/class');
define('__INC_CORE_DIR__', __ASSETS_DIR__ . '/core');
define('__CONFIG_DIR__', __ASSETS_DIR__ . '/configuration');
define('__UPDATES_DIR__', __CONFIG_DIR__ . "/updates");
define('__ERROR_LOG_DIRECTORY__', __WEB_ROOT__ . '/logs');

define('__TEMP_DIR__', sys_get_temp_dir());

define('__SQLLITE_DEFAULT_TABLES_DIR__', __CONFIG_DIR__ . '/sqllite');
define('__SQLITE_DATABASE__', __SQLITE_DIR__ . '/cwp_sqlite.db');
define('__DATABASE_DSN__', 'sqlite:' . __SQLITE_DATABASE__);



/*
 * Layout path structure in assets directory.
 */

define('__LAYOUT_DIR__', '/assets/layout');
define('__LAYOUT_ROOT__', __WEB_ROOT__ . __LAYOUT_DIR__);
define('__TEMPLATE_DIR__', __LAYOUT_ROOT__ . '/template');
define('__LATTE_TEMPLATE__', __TEMPLATE_DIR__ . '/latte');

define('__LAYOUT_HEADER__', __LAYOUT_ROOT__ . '/header.php');
define('__LAYOUT_NAVBAR__', __LAYOUT_ROOT__ . '/navbar.php');
define('__LAYOUT_FOOTER__', __LAYOUT_ROOT__ . '/footer.php');


/*
 * URL defaults.
 */
define('__URL_HOME__', 'http://' . $_SERVER['HTTP_HOST'] . __URL_PATH__);
define('__URL_LAYOUT__', __URL_HOME__ . __LAYOUT_DIR__);



//Include all necessary files.
require_once __ASSETS_DIR__ . "/includes.inc.php";

// Configure things like the database
require_once __ASSETS_DIR__ . "/configure.inc.php";

// Get settings from DB.
require_once __ASSETS_DIR__ . "/settings.inc.php";

//Footer::$theme = 'theme';
//Header::$theme = 'theme';