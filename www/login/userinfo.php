<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Template\Template;
use CWP\Utils\MediaDevice;

/**
 * CWP Media tool for load flags.
 */

require_once '../.config.inc.php';

define('TITLE', 'User Info');
// $template = new Template();
$params['EMAIL'] = $auth->getEmail();
$params['USERNAME'] = $auth->getUsername();
$params['IPADDRESS'] = $auth->getIpAddress();

MediaDevice::getHeader();
$chpwd['SUBMIT_BUTTON'] = Template::getHTML('authentication/button/submit', ['SUBMIT_VALUE' => 'change']);
$chpwd['__FORM_URL__'] = __URL_PATH__.'/login/change_pwd.php';

$chpwd['FORM_FIELD'] = Template::getHTML('authentication/forms/changepwd');

$params['CHANGE_PWD_HTML'] = Template::GetHTML('authentication/form', $chpwd);
$template->render('authentication\userinfo', $params);
MediaDevice::getFooter();
