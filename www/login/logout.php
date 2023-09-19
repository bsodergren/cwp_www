<?php
/**
 * CWP Media tool for load flags
 */

use CWP\HTML\HTMLDisplay;

require_once '../.config.inc.php';

$auth->logOut();

echo HTMLDisplay::JavaRefresh('/login/login.php', 0, 'Logged Out');
