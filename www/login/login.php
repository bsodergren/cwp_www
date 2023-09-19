<?php
/**
 * CWP Media tool for load flags
 */

use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

define('__AUTH__', false);

require_once '../.config.inc.php';

define('TITLE', 'Login');

// echo HTMLDisplay::JavaRefresh('/login/register.php', 0);
if (array_key_exists('username', $_POST)) {
    if (1 == $_POST['remember']) {
        // keep logged in for one year
        $rememberDuration = (int) (60 * 60 * 24 * 365.25);
    } else {
        // do not keep logged in after session ends
        $rememberDuration = null;
    }

    try {
        $auth->loginWithUsername($_POST['username'], $_POST['password'], $rememberDuration);
        $msg = 'Logged in';
    } catch (\Delight\Auth\InvalidEmailException  $e) {
        $msg = 'Wrong email address';
    } catch (\Delight\Auth\InvalidPasswordException $e) {
        $msg = 'Wrong password';
    } catch (\Delight\Auth\EmailNotVerifiedException $e) {
        $msg = 'Email not verified';
    } catch (\Delight\Auth\TooManyRequestsException $e) {
        $msg = 'Too many requests';
    }

    echo HTMLDisplay::JavaRefresh('/index.php', 0, $msg);
    exit;
}

MediaDevice::getHeader();

$register = Template::getHtml('authentication/button/register', []);
if (__USE_REGISTER__ == false) {
    $register = '';
}

$params['SUBMIT_BUTTON'] = Template::getHTML('authentication/button/submit', ['SUBMIT_VALUE' => 'login']);
$params['FORGOT_PWD'] = Template::getHTML('authentication/button/forgot_passwd');
$params['FORM_FIELD'] = Template::getHTML('authentication/forms/login');
$params['__FORM_URL__'] = __URL_PATH__.'/login/login.php';
$params['REGISTER'] = $register;
$template->render('authentication/form', $params);
MediaDevice::getFooter();
