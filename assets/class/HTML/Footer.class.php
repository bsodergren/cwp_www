<?php
use Sinergi\BrowserDetector\Browser;

class Footer extends Template
{
    //public $html;
    public static $theme = '';

    public static function display($template = '', $params = [])
    {
        $templateObj = new Template();
        
        if (self::$theme != ''){
            $theme_path = self::$theme . "/";
        }

        if(MediaSettings::isSet('__FOOTER_NAV_HTML__'))
        {
            $params['FOOT_NAV_PANEL'] = $templateObj->template("base/".$theme_path ."footer/settings_nav",
            ['FOOTER_NAV_HTML' => __FOOTER_NAV_HTML__]);
        }

  
        if (MediaSettings::isTrue('__SHOW_DEBUG_PANEL__')) {
            $errorArray = getErrorLogs();
            $debug_nav_link_html = '';

            foreach ($errorArray as $k => $file) {
                $file = basename($file);
                $key = str_replace(".", "_", basename($file));
                $debug_nav_links = [
                    'DEBUG_NAV_LINK_URL' => 'debug.php?log=' . $key . '',
                    'DEBUG_NAV_LINK_FILE' => $file
                ];
                $debug_nav_link_html .= $templateObj->template("base/".$theme_path ."footer/nav_item_link", $debug_nav_links);
            }
            $debug_panel_params['DEBUG_FILE_LIST'] = $debug_nav_link_html;

            $params['DEBUG_PANEL_HTML'] = $templateObj->template("base/".$theme_path ."footer/debug_panel", $debug_panel_params);
        }
        
        $params['END_JAVASCRIPT'] = Template::GetHTML("base/".$theme_path ."footer/javascript");

        echo $templateObj->template("base/".$theme_path ."footer/footer", $params);
    }
}