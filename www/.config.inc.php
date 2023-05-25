<?php
function debug(...$var)
{
    echo "<pre>" . var_export($var, 1) . "</pre>";
}

define('__PROJECT_ROOT__',dirname(__FILE__,2));


define('__COMPOSER_DIR__', __PROJECT_ROOT__ .  DIRECTORY_SEPARATOR .'vendor');
set_include_path(get_include_path() . PATH_SEPARATOR . __COMPOSER_DIR__);
require __COMPOSER_DIR__ .  DIRECTORY_SEPARATOR .'autoload.php';


use Tracy\Debugger;
use Noodlehaus\Config;
use Noodlehaus\Parser\ini;
use Nette\Utils\FileSystem;



$config_file = __PROJECT_ROOT__.DIRECTORY_SEPARATOR ."config.ini";

$conf = new Config($config_file);

if($conf['application']['debug'] == 1)
{
    Debugger::enable();
}


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

define('__APP_ROOT__',    FileSystem::normalizePath($conf['server']['root_dir']));
define('__APP_INSTALL_DIR__', $conf['server']['url_root']);
define('__WEB_ROOT__',     __APP_ROOT__ . FileSystem::normalizePath($conf['server']['web_root'] . __APP_INSTALL_DIR__));
define('__ROOT_BIN_DIR__',  __APP_ROOT__ .FileSystem::normalizePath($conf['server']['bin_dir']));
define('__SQLITE_DIR__',    __APP_ROOT__ .FileSystem::normalizePath($conf['server']['db_dir']));
define('__URL_PATH__', __APP_INSTALL_DIR__);




list($__filename) = explode("?", $_SERVER['REQUEST_URI']);
$__request_name = basename($__filename,'.php');
$__script_name = basename($_SERVER['SCRIPT_NAME'], '.php');



define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));

/*
 * Default constants for include path structure.
 *
 */
define('__ASSETS_DIR__', __WEB_ROOT__ . DIRECTORY_SEPARATOR . 'assets');
define('__INC_CLASS_DIR__', __ASSETS_DIR__ . DIRECTORY_SEPARATOR . 'class');
define('__INC_CORE_DIR__', __ASSETS_DIR__ . DIRECTORY_SEPARATOR . 'core');
define('__CONFIG_DIR__', __ASSETS_DIR__ . DIRECTORY_SEPARATOR . 'configuration');
define('__UPDATES_DIR__', __CONFIG_DIR__ . DIRECTORY_SEPARATOR . "updates");
define('__ERROR_LOG_DIRECTORY__', __PROJECT_ROOT__ . DIRECTORY_SEPARATOR . 'logs');

define('__TEMP_DIR__', sys_get_temp_dir());

define('__SQLLITE_DEFAULT_TABLES_DIR__', __CONFIG_DIR__ . DIRECTORY_SEPARATOR . 'sqllite');
define('__SQLITE_DATABASE__', __SQLITE_DIR__ . DIRECTORY_SEPARATOR . 'cwp_sqlite.db');
define('__DATABASE_DSN__', 'sqlite:' . __SQLITE_DATABASE__);



/*
 * Layout path structure in assets directory.
 */

define('__LAYOUT_DIR__', DIRECTORY_SEPARATOR . 'assets/layout');
define('__LAYOUT_ROOT__', __WEB_ROOT__ . __LAYOUT_DIR__);
define('__TEMPLATE_DIR__', __LAYOUT_ROOT__ . DIRECTORY_SEPARATOR . 'template');
define('__LATTE_TEMPLATE__', __TEMPLATE_DIR__ . DIRECTORY_SEPARATOR . 'latte');

define('__LAYOUT_HEADER__', __LAYOUT_ROOT__ . DIRECTORY_SEPARATOR . 'header.php');
define('__LAYOUT_NAVBAR__', __LAYOUT_ROOT__ . DIRECTORY_SEPARATOR . 'navbar.php');
define('__LAYOUT_FOOTER__', __LAYOUT_ROOT__ . DIRECTORY_SEPARATOR . 'footer.php');


/*
 * URL defaults.
 */
define('__URL_HOME__', 'http://' . $_SERVER['HTTP_HOST'] . __URL_PATH__);
define('__URL_LAYOUT__', __URL_HOME__ . "/assets/layout/");


//Include all necessary files.
require_once __ASSETS_DIR__ . DIRECTORY_SEPARATOR . "includes.inc.php";

// Configure things like the database
require_once __ASSETS_DIR__ . DIRECTORY_SEPARATOR . "configure.inc.php";

// Get settings from DB.
require_once __ASSETS_DIR__ . DIRECTORY_SEPARATOR . "settings.inc.php";

//Footer::$theme = 'theme';
//Header::$theme = 'theme';