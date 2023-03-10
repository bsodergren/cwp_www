<?php


use Sinergi\BrowserDetector\Browser;


class Navbar extends Template
{

    public static $theme = '';

    public static function display($template = '', $params = [])
    {
        $templateObj = new Template();

        $nav_link_html = '';
        $dropddown_menu_text = '';
        $dropdown_link_html = '';
        $navbar_menu_html = '';
        $nav_list_dir = "list";

        if (self::$theme != ''){
            $theme_path = self::$theme . "/";
        }


        $browser = new Browser();
        if ($browser->getVersion() != '57.0.2987.98') {
            $nav_list_dir = "dropdown";
        }
        $nav_links_array = json_decode(__NAVBAR_LINKS__, 1);
        foreach ($nav_links_array as $text =>  $url) {

            if (is_array($url)) {

                $dropddown_menu_text = $text;

                foreach ($url as $dropdown_text => $dropdown_url) {
                    $dropdown_link_html .= $templateObj->template(
                        "base/".$theme_path ."navbar/" . $nav_list_dir . "/navbar_link",
                        ['DROPDOWN_URL' => $dropdown_url, 'DROPDOWN_URL_TEXT' => $dropdown_text]
                    );
                }

                continue;
            }
            
            
            $nav_link_html .= $templateObj->template("base/".$theme_path ."navbar/navbar_item_link", ['NAV_LINK_URL' => $url, 'NAV_LINK_TEXT' => $text]);
        }
        
        if ($browser->getVersion() == '57.0.2987.98') {
            define('__FOOTER_NAV_HTML__',$dropdown_link_html);
        }


        $navbar_menu_html = $templateObj->template("base/".$theme_path ."navbar/" . $nav_list_dir . "/navbar_menu", [
            'NAV_BAR_LINKS' => $nav_link_html,
            'DROPDOWN_LINKS' => $dropdown_link_html,
            'DROPDOWN_TEXT' => $dropddown_menu_text,
        ]);
        $params['NAVBAR_MENU_HTML'] = $navbar_menu_html;
        return $templateObj->template("base/".$theme_path ."navbar/navbar", $params);
    }
}
