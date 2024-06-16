<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\HTML\HTMLDisplay;

require_once '../.config.inc.php';

$auth->logOut();

echo Elements::JavaRefresh('/login/login.php', 0, 'Logged Out');
