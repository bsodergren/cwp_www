<?php
/**
 * CWP Media Load Flag Creator
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
    // 'Test' => '/test.php',
    'Home' => '/index.php',
    'Create Job' => '/create/index.php',
    'Import' => '/import.php',
    'Trim Sizes' => '/settings/trim.php',
    'Settings' => [
        'Market' => '/create/view.php?v=market',
        'Publications' => '/create/view.php?v=publication',
        'Destinations' => '/create/view.php?v=destination',
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


define('__USE_REGISTER__', Bootstrap::$CONFIG['application']['register']);
