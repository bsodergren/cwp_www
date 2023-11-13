<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\HTML\HTMLDisplay;

define('__AUTH__', true);

require_once '../.config.inc.php';

if (array_key_exists('submit', $_POST) && 'change' == $_POST['submit']) {
    try {
        $auth->changePassword($_POST['oldPassword'], $_POST['newPassword']);

        $msg = 'Password has been changed';
    } catch (\Delight\Auth\NotLoggedInException $e) {
        $msg = 'Not logged in';
    } catch (\Delight\Auth\InvalidPasswordException $e) {
        $msg = 'Invalid password(s)';
    } catch (\Delight\Auth\TooManyRequestsException $e) {
        $msg = 'Too many requests';
    }
    echo HTMLDisplay::JavaRefresh('/login/userinfo.php', 0, $msg);
}
