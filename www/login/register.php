<?php
/**
 * CWP Media tool for load flags
 */

use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaMailer;
use CWP\Utils\MediaDevice;

/*
 * CWP Media tool for load flags
 */

define('__AUTH__', false);

require_once '../.config.inc.php';

if (__NO_REGISTER__ == true) {
    echo HTMLDisplay::JavaRefresh('/login/login.php', 0, $msg);
    exit;
}

define('TITLE', 'Register');

if (!isset($_POST['email'])) {
    MediaDevice::getHeader();
    $template->render('authentication/register', ['__FORM_URL__' => __URL_PATH__.'/login/register.php']);
    MediaDevice::getFooter();
    exit;
}

$mail = new MediaMailer();

$mail->recpt($_POST['email'], $_POST['name']);     // Add a recipient

try {
    $userId = $auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) use ($mail) {
        $mail->subject('Verification email');
        $html = 'Please verify your email <br>'.\PHP_EOL;
        $html .= 'By clicking the link below <br>'.\PHP_EOL;
        $html .= ' <a href="'.__URL_HOME__.'/login/verify.php?selector='.urlencode($selector).'&token='.urlencode($token).'">click here</a> <br>'.\PHP_EOL;
        $mail->Body($html);
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
