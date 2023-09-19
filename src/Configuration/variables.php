<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Core\Bootstrap;

$debug_string = '';

if (__DEBUG__ == 1) {
    define('__DEBUG_STR__', "<span class='fs-6 text-success'>Debug Enabled ".__DEVICE__.'</span>');
}

define('APP_NAME', Bootstrap::$CONFIG['application']['name']);
define('APP_ORGANIZATION', 'cwp');
define('APP_OWNER', 'bjorn');

list($__filename) = explode('?', $_SERVER['REQUEST_URI']);
$__request_name = basename($__filename, '.php');
$__script_name = basename($_SERVER['SCRIPT_NAME'], '.php');

define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));
$nav_bar_links = [
    'Home' => '/index.php',
    'Import' => '/import.php',
    'Trim Sizes' => '/settings/trim.php',
    'Settings' => [
        'Paper' => '/paper.php',
        'Language' => '/settings/language.php',
        'Local Settings' => '/settings/local.php',
        'Update' => '/updater/update.php',
    ],
];

if (__USE_AUTHENTICATION__ == true) {
    if ($auth->isLoggedIn()) {
        $nav_bar_links['Settings']['User Info'] = '/login/userinfo.php';
        $nav_bar_links['Logout'] = '/login/logout.php';
    } else {
        $nav_bar_links = [];
        $nav_bar_links['Login'] = '/login/login.php';
    }
}

define('__NAVBAR_LINKS__', $nav_bar_links);

const STREAM_CLASS = 'show test-nowrap px-5 rounded-pill';

define('MSG_CLASS', 'bg-primary bg-opacity-75 w-75 fs-3 '.STREAM_CLASS);
define('HEADER_CLASS', 'bg-success bg-opacity-50 w-50 mx-5 fs-6 '.STREAM_CLASS);

define('__NO_REGISTER__', Bootstrap::$CONFIG['application']['register']);
