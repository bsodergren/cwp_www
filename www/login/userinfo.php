<?php
/**
 * CWP Media tool for load flags
 */

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
$template->render('authentication\userinfo', $params);
MediaDevice::getFooter();
