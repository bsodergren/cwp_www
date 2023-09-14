<?php
/**
 * CWP Media tool
 */

namespace CWP\HTML\Mobile;

use CWP\HTML\Template;
use CWP\Media\MediaSettings;

/**
 * CWP Media tool.
 */

/**
 * CWP Media tool.
 */
class Footer extends Template
{
    // public $html;

    public static function display($template = '', $params = [])
    {
        $templateObj = new Template();

        [$installed,$latest] = self::VersionText();
        $version_html = $templateObj->template('base/footer/version_current', ['VERSION' => $installed]);
        if (null != $latest) {
            $version_html = $templateObj->template('base/footer/version_latest', ['VERSION' => $latest]);
        }

        if (MediaSettings::isSet('__FOOTER_NAV_HTML__')) {
            $params['FOOT_NAV_PANEL'] = $templateObj->template(
                'base/footer/settings_nav',
                ['FOOTER_NAV_HTML' => __FOOTER_NAV_HTML__,
                'VERSIONS_HTML' => $version_html,
                ]
            );
        }


        $params['END_JAVASCRIPT'] = Template::GetHTML('base/footer/javascript');
        echo $templateObj->template('base/footer/footer', $params);
    }
}
