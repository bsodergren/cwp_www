<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Template\Desktop;

use CWP\Core\MediaSettings;
use CWP\Template\HTMLDocument;
use CWP\Template\Template;

class Footer extends HTMLDocument
{
    // public $html;

    public static function display($template = '', $params = [])
    {
        if (MediaSettings::isSet('__FOOTER_NAV_HTML__')) {
            $params['FOOT_NAV_PANEL'] = Template::GetHTML(
                'base/footer/settings_nav',
                ['FOOTER_NAV_HTML' => __FOOTER_NAV_HTML__,
                'VERSIONS_HTML' => self::_footerVersionUpdates(),
                ]
            );
        }

        $params['END_JAVASCRIPT'] = Template::GetHTML('base/footer/javascript');
        echo Template::GetHTML('base/footer/footer', $params);
    }
}
