<?php

define('__PROJECT_ROOT__', dirname(__FILE__, 2));

define('__COMPOSER_DIR__', __PROJECT_ROOT__.DIRECTORY_SEPARATOR.'vendor');
set_include_path(get_include_path().PATH_SEPARATOR.__COMPOSER_DIR__);
require __COMPOSER_DIR__.DIRECTORY_SEPARATOR.'autoload.php';

use Nette\Utils\FileSystem;
use Noodlehaus\Config;
use Noodlehaus\Parser\ini;
use Tracy\Debugger;

$config_file = __PROJECT_ROOT__.DIRECTORY_SEPARATOR.'config.ini';

$conf = new Config($config_file);

if ($conf['application']['debug'] == 1) {
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

define('__APP_ROOT__', FileSystem::normalizePath($conf['server']['root_dir']));
define('__APP_INSTALL_DIR__', $conf['server']['url_root']);
define('__WEB_ROOT__', __APP_ROOT__.FileSystem::normalizePath($conf['server']['web_root'].__APP_INSTALL_DIR__));
define('__ROOT_BIN_DIR__', FileSystem::normalizePath($conf['server']['bin_dir']));
define('__SQLITE_DIR__', __APP_ROOT__.FileSystem::normalizePath($conf['server']['db_dir']));
define('__URL_PATH__', __APP_INSTALL_DIR__);

list($__filename) = explode('?', $_SERVER['REQUEST_URI']);
$__request_name = basename($__filename, '.php');
$__script_name = basename($_SERVER['SCRIPT_NAME'], '.php');

define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));

/*
 * Default constants for include path structure.
 *
 */
define('__ASSETS_DIR__', __WEB_ROOT__.DIRECTORY_SEPARATOR.'assets');
define('__INC_CLASS_DIR__', __ASSETS_DIR__.DIRECTORY_SEPARATOR.'class');
define('__INC_CORE_DIR__', __ASSETS_DIR__.DIRECTORY_SEPARATOR.'core');
define('__CONFIG_DIR__', __ASSETS_DIR__.DIRECTORY_SEPARATOR.'configuration');
define('__UPDATES_DIR__', __CONFIG_DIR__.DIRECTORY_SEPARATOR.'updates');
define('__ERROR_LOG_DIRECTORY__', __PROJECT_ROOT__.DIRECTORY_SEPARATOR.'logs');

define('__TEMP_DIR__', sys_get_temp_dir());

define('__SQLLITE_DEFAULT_TABLES_DIR__', __CONFIG_DIR__.DIRECTORY_SEPARATOR.'sqllite');
define('__SQLITE_DATABASE__', __SQLITE_DIR__.DIRECTORY_SEPARATOR.'cwp_sqlite.db');
define('__DATABASE_DSN__', 'sqlite:'.__SQLITE_DATABASE__);

/*
 * Layout path structure in assets directory.
 */

define('__LAYOUT_DIR__', DIRECTORY_SEPARATOR.'assets/layout');
define('__LAYOUT_ROOT__', __WEB_ROOT__.__LAYOUT_DIR__);
define('__TEMPLATE_DIR__', __LAYOUT_ROOT__.DIRECTORY_SEPARATOR.'template');
define('__LATTE_TEMPLATE__', __TEMPLATE_DIR__.DIRECTORY_SEPARATOR.'latte');

define('__LAYOUT_HEADER__', __LAYOUT_ROOT__.DIRECTORY_SEPARATOR.'header.php');
define('__LAYOUT_NAVBAR__', __LAYOUT_ROOT__.DIRECTORY_SEPARATOR.'navbar.php');
define('__LAYOUT_FOOTER__', __LAYOUT_ROOT__.DIRECTORY_SEPARATOR.'footer.php');

/*
 * URL defaults.
 */
define('__URL_HOME__', 'http://'.$_SERVER['HTTP_HOST'].__URL_PATH__);
define('__URL_LAYOUT__', __URL_HOME__.'/assets/layout/');

$__conf_pathCheck = __APP_ROOT__.'/configCheck.php';
$__conf_checked = __APP_ROOT__.'/.config.true';

$includes[] = __ASSETS_DIR__.DIRECTORY_SEPARATOR.'includes.inc.php';
$includes[] = __ASSETS_DIR__.DIRECTORY_SEPARATOR.'configure.inc.php';
$includes[] = __ASSETS_DIR__.DIRECTORY_SEPARATOR.'settings.inc.php';

if (! file_exists($__conf_pathCheck)) {
    die('Root path not set correctly');
}

if (! file_exists($__conf_checked)) {
    require_once $__conf_pathCheck;
}

foreach ($includes as $file) {
    require_once $file;
}