<?php
/**
 * Command like Metatag writer for video files.
 */

use CWP\HTML\HTMLDisplay;

require_once '../.config.inc.php';

$auth->logOut();

echo HTMLDisplay::JavaRefresh('/login/login.php', 0, 'Logged Out');
