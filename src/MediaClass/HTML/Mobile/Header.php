<?php
/**
 * CWP Media tool.
 */

namespace CWP\HTML\Application;

use CWP\HTML\HTMLDocument;
use CWP\HTML\Template;

class Header extends HTMLDocument
{
    public static function display($template = '', $params = [])
    {
        [$js,$onload] = self::header_JS();

        define('__CUSTOM_JS__', $js);
        define('__ONLOAD__', $onload);

        [$params['BOOTSTRAP'] ,$params['DEFAULT_CSS']] = self::header_CSS();
        $params['__NAVBAR__'] = self::_getNavbar();
        $params['UPDATES_HTML'] = self::_headerVersionUpdates();
        $params['__MSG__'] = self::displayMsg();

        echo Template::GetHTML('base/header/header', $params);
    }
}
