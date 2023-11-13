<?php
/**
 * Command like Metatag writer for video files.
 */

use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

define('__AUTH__', false);

require_once '../.config.inc.php';

if (array_key_exists('submit', $_POST) && 'reset' == $_POST['submit']) {
    try {
        $auth->resetPassword($_POST['selector'], $_POST['token'], $_POST['password']);

        $msg = 'Password has been reset';
    } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
        $msg = 'Invalid token';
    } catch (\Delight\Auth\TokenExpiredException $e) {
        $msg = 'Token expired';
    } catch (\Delight\Auth\ResetDisabledException $e) {
        $msg = 'Password reset is disabled';
    } catch (\Delight\Auth\InvalidPasswordException $e) {
        $msg = 'Invalid password';
    } catch (\Delight\Auth\TooManyRequestsException $e) {
        $msg = 'Too many requests';
    }
    echo HTMLDisplay::JavaRefresh('/login/login.php', 0, $msg);
}

if (array_key_exists('token', $_GET)) {
    try {
        $auth->canResetPasswordOrThrow($_GET['selector'], $_GET['token']);

        $tokens['SELECTOR'] = $_GET['selector'];
        $tokens['TOKEN'] = $_GET['token'];

        $params['SUBMIT_BUTTON'] = Template::getHTML('authentication/button/submit', ['SUBMIT_VALUE' => 'reset']);
        $params['FORM_FIELD'] = Template::getHTML('authentication/forms/reset_form', $tokens);
        $params['__FORM_URL__'] = __URL_PATH__.'/login/reset_passwd.php';

        MediaDevice::getHeader();
        $template->render('authentication/form', $params);
        MediaDevice::getFooter();
    } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
        $msg = 'Invalid token';
    } catch (\Delight\Auth\TokenExpiredException $e) {
        $msg = 'Token expired';
    } catch (\Delight\Auth\ResetDisabledException $e) {
        $msg = 'Password reset is disabled';
    } catch (\Delight\Auth\TooManyRequestsException $e) {
        $msg = 'Too many requests';
    }

    exit;
}
