<?php
/**
 * CWP Media tool for load flags
 */

use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaMailer;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

/*
 * CWP Media tool for load flags
 */

define('__AUTH__', false);

require_once '../.config.inc.php';

if (__USE_REGISTER__ == false) {
    echo HTMLDisplay::JavaRefresh('/login/login.php', 0, $msg);
    exit;
}

define('TITLE', 'Register');

if (!isset($_POST['email'])) {


    $params['SUBMIT_BUTTON'] = Template::getHTML('authentication/button/submit', ['SUBMIT_VALUE' => 'register']);
    $params['FORM_FIELD'] = Template::getHTML('authentication/forms/register');
    $params['__FORM_URL__'] = __URL_PATH__.'/login/register.php';
    
    MediaDevice::getHeader();
    $template->render('authentication/form', $params);
    MediaDevice::getFooter();
    exit;
}

$mail = new MediaMailer();

$mail->recpt($_POST['email'], $_POST['name']);     // Add a recipient

try {
    $userId = $auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) use ($mail) {
        $mail->subject('Verification email');

        $params['VERIFY_LINK'] = __URL_HOME__.'/login/verify.php?selector='.urlencode($selector).'&token='.urlencode($token);

        $mail->Body(Template::getHtml('authentication/email/verify', $params));
        $mail->mail();
    });

    $msg = 'We have signed up a new user with the ID '.$userId;
} catch (\Delight\Auth\InvalidEmailException $e) {
    $msg = 'Invalid email address';
} catch (\Delight\Auth\InvalidPasswordException $e) {
    $msg = 'Invalid password';
} catch (\Delight\Auth\UserAlreadyExistsException $e) {
    $msg = 'User already exists';
} catch (\Delight\Auth\TooManyRequestsException $e) {
    $msg = 'Too many requests';
}
 echo HTMLDisplay::JavaRefresh('/login/login.php', 0, $msg);
