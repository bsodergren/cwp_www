<?php
/**
 * CWP Media tool.
 */

namespace CWP\HTML\Application;

use CWP\HTML\HTMLDocument;
use CWP\HTML\Template;
use CWP\Media\MediaSettings;

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
