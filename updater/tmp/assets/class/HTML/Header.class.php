<?php



class Header
{

    public static function display($template = "", $params = [])
    {

        $path = "/" . __SCRIPT_NAME__;
        if (MediaSettings::isTrue('__FORM_POST__')) {
            $path = "/" . __FORM_POST__;
        }

            if (file_exists(__TEMPLATE_DIR__ . $path . "/javascript.html")) {
                define('__CUSTOM_JS__', Template::GetHTML($path . "/javascript"));
            }

            if (file_exists(__TEMPLATE_DIR__ . $path . "/onload.html")) {
                define('__ONLOAD__', Template::GetHTML($path . "/onload"));
            }
        if (!MediaSettings::isTrue('NO_NAV')) {
            $params['__NAVBAR__'] = Navbar::Display();
        }

        $templateObj = new template();
        echo $templateObj->template("base/header/header", $params);
    }
}