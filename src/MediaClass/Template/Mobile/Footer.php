<?php
/**
 * CWP Media tool.
 */

namespace CWP\Template\Mobile;

use CWP\Template\Template;
use CWP\Template\HTMLDocument;
use CWP\Utils\MediaDevice;
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

        $params['JS_FOOTER_SRC'] = MediaDevice::getAssetURL('js', ['js/main.js',
        "vendor/bootstrap/js/bootstrap.bundle.min.js",
        "vendor/purecounter/purecounter_vanilla.js",
        "vendor/glightbox/js/glightbox.min.js",
        "vendor/swiper/swiper-bundle.min.js",
        "vendor/aos/aos.js",
        "vendor/php-email-form/validate.js"
        ]);

        $params['END_JAVASCRIPT'] = Template::GetHTML('base/footer/javascript');
        echo Template::GetHTML('base/footer/footer', $params);
    }
}
