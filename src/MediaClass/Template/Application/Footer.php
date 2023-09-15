<?php
/**
 * CWP Media tool.
 */

namespace CWP\Template\Application;

use CWP\Template\HTMLDocument;
use CWP\Template\Template;
use CWP\Core\MediaSettings;

/**
 * CWP Media tool.
 */

/**
 * CWP Media tool.
 */
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
