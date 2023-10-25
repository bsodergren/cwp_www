<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Template\Template;
use CWP\Utils\MediaDevice;

require_once '.config.inc.php';

MediaDevice::getHeader();

if (array_key_exists('type', $_GET)) {
    $type = $_GET['type'];
}

if (array_key_exists('code', $_GET)) {
    $code = $_GET['code'];
}
if (array_key_exists('message', $_GET)) {
    $message = $_GET['message'];
}

if ('Dropbox' == $type) {
    switch ($code) {
        case '401':
            Template::echo('error/dropbox/'.$code, ['TOKEN' => __DROPBOX_AUTH_TOKEN__]);
            break;
        default:
            Template::echo('error/dropbox/default', ['CODE' => $code, 'MSG' => $message]);
            break;
    }
}

MediaDevice::getFooter();
