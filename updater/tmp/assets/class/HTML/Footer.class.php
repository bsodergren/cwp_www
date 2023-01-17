<?php

class Footer
{
    //public $html;

    public static function display($template = '', $params = [])
    {
        $templateObj = new template();
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
                $debug_nav_link_html .= $templateObj->template("base/footer/nav_item_link", $debug_nav_links);
            }
            $debug_panel_params['DEBUG_FILE_LIST'] = $debug_nav_link_html;

            $params['DEBUG_PANEL_HTML'] = $templateObj->template("base/footer/debug_panel", $debug_panel_params);
        }

        echo $templateObj->template("base/footer/footer", $params);
    }
}