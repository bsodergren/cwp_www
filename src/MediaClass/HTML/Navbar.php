<?php
/**
 * CWP Media tool
 */

namespace CWP\HTML;

/*
 * CWP Media tool
 */

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
        $dropdown_divider = null;
        [$installed,$latest] = self::VersionText();

        $browser = new Browser();
        if ('57.0.2987.98' != $browser->getVersion()) {
            $nav_list_dir = 'dropdown';
            $dropdown_divider = '  <li><hr class="dropdown-divider"></li>';
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
        $dropdown_link_html .= $dropdown_divider;
        $latest_version_html = '';
        if (null != $latest) {
            $dropdown_link_html .= $templateObj->template(
                'base/navbar/'.$nav_list_dir.'/navbar_item',
                ['DROPDOWN_TEXT' => 'New! '.$latest]);
                if ('57.0.2987.98' != $browser->getVersion()) {
            $latest_version_html = $templateObj->template('base/footer/version_latest', ['VERSION' => $latest]);
                }
        }

        $dropdown_link_html .= $templateObj->template(
            'base/navbar/'.$nav_list_dir.'/navbar_item',
            ['DROPDOWN_TEXT' => 'Version '.$installed]
        );

        $navbar_right_dropdown = $templateObj->template('base/navbar/'.$nav_list_dir.'/navbar_dropdown', [
                    'DROPDOWN_LINKS' => $dropdown_link_html,
                'DROPDOWN_TEXT' => $dropddown_menu_text]);

        $params['NAVBAR_LEFT_HTML'] = $templateObj->template('base/navbar/navbar_left', []);
        $params['NAVBAR_CENTER_HTML'] = $templateObj->template('base/navbar/navbar_center', ['NAVBAR_CENTER_LIST' => $latest_version_html]);
        $params['NAVBAR_RIGHT_HTML'] = $templateObj->template('base/navbar/navbar_right', [
            'NAVBAR_RIGHT_LIST' => $nav_link_html,
            'NAVBAR_RIGHT_DROPDOWN' => $navbar_right_dropdown,
        ]);

        return $templateObj->template('base/navbar/navbar', $params);
    }
}
