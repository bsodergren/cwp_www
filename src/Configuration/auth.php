<?php
/**
 * CWP Media tool for load flags
 */

use CWP\HTML\HTMLDisplay;

/**
 * CWP Media tool for load flags.
 */
$db = new PDO(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);
$auth = new \Delight\Auth\Auth($db);
if (!defined('__AUTH__')) {
    define('__AUTH__', true);
}

if (__AUTH__ == true) {
    if (!$auth->isLoggedIn()) {
        echo HTMLDisplay::JavaRefresh('/login/login.php', 0);
        exit;
    }
}
