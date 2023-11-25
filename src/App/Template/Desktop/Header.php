<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Template\Desktop;

use CWP\Template\HTMLDocument;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

class Header extends HTMLDocument
{
    public static function display($template = '', $params = [])
    {
        [$js,$onload]                                  = self::header_JS();

        \define('__CUSTOM_JS__', $js);
        \define('__ONLOAD__', $onload);
        $params['FAV_ICON']                            = MediaDevice::getAssetURL('image', ['/images/favicon.png'],false,false);
        $params['CSS_SRC']                             = MediaDevice::getAssetURL('css', ['css/app.css'],false,false);
        $params['JS_SRC']                              = MediaDevice::getAssetURL('js', ['js/app.js', 'js/jquery-3.4.1.min.js'],false,false);

        [$params['BOOTSTRAP'] ,$params['DEFAULT_CSS']] = self::header_CSS();
        $params['__NAVBAR__']                          = self::_getNavbar();
        $params['UPDATES_HTML']                        = self::_headerVersionUpdates();
        $params['__MSG__']                             = self::displayMsg();

        echo Template::GetHTML('base/header/header', $params,false,false);
    }
}
