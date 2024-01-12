<?php
/**
 * CWP Media Load Flag Creator.
 */
define('__URL_HOST__', 'http://'.$_SERVER['HTTP_HOST']);
define('__URL_HOME__', __URL_HOST__.$_SERVER['CONTEXT_PREFIX']);
define('__URL_LAYOUT__', __URL_HOME__.'/assets/themes');

define('__UPDATER_VERSION__', __URL_HOME__.'/updater/version.php');
if (__DEBUG__ == 1) {
    define('__UPDATER_REFRESH__', 999999 * 1000);
} else {
    define('__UPDATER_REFRESH__', 60 * 1000);
}

define('__CURRENT_URL__', __URL_HOME__.'/index.php');
define('__PROCESS_FORM__', __URL_HOME__.'/process.php');
// define('__PROCESS_FORM__', __URL_HOME__.'/'.basename($_SERVER['SCRIPT_NAME']));
