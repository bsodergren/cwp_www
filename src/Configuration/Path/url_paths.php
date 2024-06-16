<?php
/**
 * CWP Media Load Flag Creator.
 */

define('__URL_PATH__', APP_HOME);
define('__URL_ROOT__', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].__URL_PATH__);
define('__URL_HOME__',__URL_ROOT__);
define('__URL_ASSETS__', __URL_ROOT__.'/assets');


define('__UPDATER_VERSION__', __URL_ROOT__.'/updater/version.php');
if (__DEBUG__ == 1) {
    define('__UPDATER_REFRESH__', 999999 * 1000);
} else {
    define('__UPDATER_REFRESH__', 60 * 1000);
}

define('__CURRENT_URL__', __URL_ROOT__.'/index.php');
define('__PROCESS_FORM__', __URL_ROOT__.'/process.php');
// define('__PROCESS_FORM__', __URL_ROOT__.'/'.basename($_SERVER['SCRIPT_NAME']));
