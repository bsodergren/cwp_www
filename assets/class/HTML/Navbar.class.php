<?php

class Navbar
{

    public static function display($template = '', $params = [])
    {
        $templateObj = new template();

        $nav_link_html = '';

        $nav_links_array = json_decode(__NAVBAR_LINKS__);
        foreach ($nav_links_array as $text =>  $url) {
            $nav_link_html .= $templateObj->template("base/navbar/navbar_item_link", ['NAV_LINK_URL' => $url, 'NAV_LINK_TEXT' => $text]);
        }

        $params['NAV_BAR_LINKS'] = $nav_link_html;

        return $templateObj->template("base/navbar/navbar", $params);
    }
}
