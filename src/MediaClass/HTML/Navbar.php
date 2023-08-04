<?php
/**
 * CWP Media tool
 */

namespace CWP\HTML;

/*
 * CWP Media tool
 */

use CWP\Media\Update\AppUpdate;
use Sinergi\BrowserDetector\Browser;

class Navbar extends Template
{
    public static function display($template = '', $params = [])
    {
        $templateObj = new Template();

        $nav_link_html = '';
        $dropddown_menu_text = '';
        $dropdown_link_html = '';
        $nav_list_dir = 'list';

        $browser = new Browser();
        if ('57.0.2987.98' != $browser->getVersion()) {
            $nav_list_dir = 'dropdown';

            [$installed,$latest] = self::VersionText();
            $version_html = $templateObj->template('base/navbar/version_current', ['VERSION' => $installed]);
            if (null != $latest) {
                $version_html .= $templateObj->template('base/navbar/version_latest', ['VERSION' => $latest]);
            }

            $params['VERSION_INFO'] = $version_html;
        }
        $nav_links_array = array_merge(__DEV_LINKS__, __NAVBAR_LINKS__);
        foreach ($nav_links_array as $text => $url) {
            if (is_array($url)) {
                $dropddown_menu_text = $text;

                foreach ($url as $dropdown_text => $dropdown_url) {
                    $dropdown_link_html .= $templateObj->template(
                        'base/navbar/'.$nav_list_dir.'/navbar_link',
                        ['DROPDOWN_URL' => $dropdown_url, 'DROPDOWN_URL_TEXT' => $dropdown_text]
                    );
                }

                continue;
            }

            $nav_link_html .= $templateObj->template('base/navbar/navbar_item_link', ['NAV_LINK_URL' => $url, 'NAV_LINK_TEXT' => $text]);
        }
        if ('57.0.2987.98' == $browser->getVersion()) {
            define('__FOOTER_NAV_HTML__', $dropdown_link_html);
        }

        $params['NAVBAR_MENU_HTML'] = $templateObj->template('base/navbar/'.$nav_list_dir.'/navbar_menu', [
            'NAV_BAR_LINKS' => $nav_link_html,
            'DROPDOWN_LINKS' => $dropdown_link_html,
            'DROPDOWN_TEXT' => $dropddown_menu_text,
        ]);



        return $templateObj->template('base/navbar/navbar', $params);
    }
}
