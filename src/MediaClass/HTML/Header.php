<?php
/**
 * CWP Media tool
 */

namespace CWP\HTML;

use CWP\Media\Media;
use CWP\Media\MediaSettings;

/**
 * CWP Media tool.
 */

/*
 * CWP Media tool.
 */

class Header extends Template
{
    public static function display($template = '', $params = [])
    {
        $path = '/'.__SCRIPT_NAME__;
        if (MediaSettings::isTrue('__FORM_POST__')) {
            $path = '/'.__FORM_POST__;
        }

        if (file_exists(__TEMPLATE_DIR__.$path.'/javascript.html')) {
            define('__CUSTOM_JS__', Template::GetHTML($path.'/javascript'));
        }

        if (file_exists(__TEMPLATE_DIR__.$path.'/onload.html')) {
            define('__ONLOAD__', Template::GetHTML($path.'/onload'));
        }
        if (!MediaSettings::isTrue('NO_NAV')) {
            $params['__NAVBAR__'] = Navbar::Display();
        }

        $params['BOOTSTRAP'] = Template::GetHTML('base/header/bootstrap_5');
        if (!array_key_exists('CUSTOM_CSS', $params)) {
            $params['DEFAULT_CSS'] = Template::GetHTML('base/header/css');
        }

        $templateObj = new Template();

        if (Media::$AutoUpdate->newVersionAvailable()) {
            $params['UPDATES_HTML'] = $templateObj->template('base/header/updates', [
            'VERSION_UPDATES' =>Media::$AutoUpdate->getLatestVersion()]);
        }

        $params['__MSG__'] = self::displayMsg();

        echo $templateObj->template('base/header/header', $params);
    }

    public static function displayMsg()
    {
        if (isset($GLOBALS)) {
            if (is_array($GLOBALS['_REQUEST'])) {
                if (array_key_exists('msg', $GLOBALS['_REQUEST'])) {
                    return Template::GetHTML('base/header/return_msg', ['MSG' => urldecode($GLOBALS['_REQUEST']['msg'])]);
                }
            }
        }

        return '';
    }
}
