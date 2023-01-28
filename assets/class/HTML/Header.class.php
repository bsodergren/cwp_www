<?php



class Header extends Template
{

    public static $theme = '';

    public static function display($template = "", $params = [])
    {

        if (self::$theme != ''){
            Navbar::$theme = self::$theme;
            $theme_path = self::$theme . "/";
        }
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

      

        //$params['BOOTSTRAP'] = Template::GetHTML("base/header/theme");
        $params['BOOTSTRAP'] = Template::GetHTML("base/".$theme_path ."header/bootstrap_5");

        $templateObj = new Template();
        echo $templateObj->template("base/".$theme_path ."header/header", $params);
    }
}