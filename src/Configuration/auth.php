<?php
/**
 * CWP Media Load Flag Creator.
 */

use CWP\Core\Bootstrap;
use CWP\HTML\HTMLDisplay;

/*
 * CWP Media tool for load flags.
 */

// if (__DEBUG__ == 0) {
define('__USE_AUTHENTICATION__', Bootstrap::$CONFIG['application']['authenticate']);
// } else {
// define('__USE_AUTHENTICATION__', false);
// }

if (__USE_AUTHENTICATION__ == true) {
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
}
