<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaMailer;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

define('__AUTH__', false);

require_once '../.config.inc.php';
define('TITLE', 'Register');

if (!isset($_POST['email'])) {
    MediaDevice::getHeader();

    $params['SUBMIT_BUTTON'] = Template::getHTML('authentication/button/submit', ['SUBMIT_VALUE' => 'reset']);
    $params['FORM_FIELD'] = Template::getHTML('authentication/forms/reset_passwd');
    $params['__FORM_URL__'] = __URL_PATH__.'/login/forgot.php';
    $template->render('authentication/form', $params);
    MediaDevice::getFooter();
    exit;
}

$mail = new MediaMailer();

$mail->recpt($_POST['email'], '');     // Add a recipient

try {
    $auth->forgotPassword($_POST['email'], function ($selector, $token) use ($mail) {
        $mail->subject('Reset your password');
        $params['VERIFY_LINK'] = __URL_ROOT__.'/login/reset_passwd.php?selector='.urlencode($selector).'&token='.urlencode($token);
        $mail->Body(Template::getHtml('authentication/email/reset', $params));
        $mail->mail();
        $msg = 'Password Recovery email sent';
    });
} catch (\Delight\Auth\InvalidEmailException $e) {
    $msg = 'Invalid email address';
} catch (\Delight\Auth\InvalidPasswordException $e) {
    $msg = 'Invalid password';
} catch (\Delight\Auth\UserAlreadyExistsException $e) {
    $msg = 'User already exists';
} catch (\Delight\Auth\TooManyRequestsException $e) {
    $msg = 'Too many requests';
}
 echo Elements::JavaRefresh('/login/login.php', 0, $msg);
