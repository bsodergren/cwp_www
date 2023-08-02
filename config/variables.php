<?php
/**
 * CWP Media tool
 */

use CWP\Bootstrap;

define('APP_NAME', Bootstrap::$CONFIG['application']['name']);
define('APP_ORGANIZATION', 'cwp');
define('APP_OWNER', 'bjorn');

list($__filename) = explode('?', $_SERVER['REQUEST_URI']);
$__request_name   = basename($__filename, '.php');
$__script_name    = basename($_SERVER['SCRIPT_NAME'], '.php');

define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));
$nav_bar_links    = [
    'Home'         => '/index.php',
    'Import'       => '/import.php',
    'Trim Sizes'   => '/settings/trim.php',
    'Settings'     => [
        'Paper'          => '/paper.php',
        'Language'       => '/settings/language.php',
        'Local Settings' => '/settings/local.php',
    ],
];
if (__NO_UPDATES__ == false) {
    $nav_bar_links['Settings']['Update']         = '/updater/update.php';
}

define('__NAVBAR_LINKS__', $nav_bar_links);
