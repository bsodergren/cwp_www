<?php

namespace  CWPDisplay\Template;

use  CWPDisplay\HTML\Colors;
use  CWPDisplay\Template\Render;

/**
 * plex web viewer.
 */

/**
 * plex web viewer.
 */
class Display
{
    public $_SERVER;
    public $_SESSION;
    public $_REQUEST;
    public static $CSS_THEMES = [];
    public static $CrubURL = [];
    private $model_popup = '';
    private $model_buttons = [];
    public static $Random;

    public function __construct()
    {
        global $_SESSION;
        global $_REQUEST;
        global $_SERVER;

        $this->_SESSION = $_SESSION;
        $this->_REQUEST = $_REQUEST;
        $this->_SERVER = $_SERVER;
    }

    public static function directory_navlinks($url, $text, $request_uri = '', $class = '', $additional = '')
    {
        global $_SESSION;
        global $_REQUEST;

        $request_string = '';

        if ('' != $request_uri) {
            $request_string = $request_uri;
        }
        if ('' != $class) {
            $class = ' class="'.$class.'"';
        }

        // $link_url = $url . "?" . $request_key ."&genre=".$_REQUEST["genre"]."&". ;
        $html = "<li class='nav-item'><a href='".$url.$request_string."' ".$class.' '.$additional.'>'.$text.'</a></li>';

        return $html;
    } // end directory_navlinks()

    public static function navbar_left_links($url, $text, $js = '')
    {
        $style = '';
        global $_SESSION;

        if ($text == $_SESSION['library']) {
            $style = ' style="background:#778899"';
        }

        $array = [
            'MENULINK_URL' => $url,
            'MENULINK_JS' => $style,
            'MENULINK_TEXT' => $text,
        ];

        return Render::html('base/navbar/library_links', $array);
    } // end navbar_left_links()

    public static function echo($text, $var = '')
    {

        $pre_style = 'style="border: 1px solid #ddd;border-left: 3px solid #f36d33;color: #666;page-break-inside: avoid;font-family: monospace;font-size: 15px;line-height: 1.6;margin-bottom: 1.6em;max-width: 100%;overflow: auto;padding: 1em 1.5em;display: block;word-wrap: break-word;"';
        $div_style = 'style="display: inline-block;width: 100%;border: 1px solid #000;text-align: left;font-size:1.5rem;"';
        $colors = new Colors();
        $is_array = false;

        if (\is_array($text)) {
            $var = $text;
            $text = 'Array';
        }

        if (\is_array($var)) {
            $var = var_export($var, 1);
            $var = $colors->getColoredHTML($var, 'green');
            $var = "<pre {$pre_style}>".$var.'</pre>';
            $is_array = true;
        } else {
            $var = $colors->getColoredHTML($var, 'green');
        }

        $text = $colors->getColoredHTML($text);

        echo "<div {$div_style}>".$text.' '.$var."</div><br>\n";
    }

    public function model($text, $var = '')
    {
        $pre_style = 'style="border: 1px solid #ddd;border-left: 3px solid #f36d33;color: #666;page-break-inside: avoid;font-family: monospace;font-size: 15px;line-height: 1.6;margin-bottom: 1.6em;max-width: 100%;overflow: auto;padding: 1em 1.5em;display: block;word-wrap: break-word;"';

        $is_array = false;

        if (\is_array($text)) {
            $var = $text;
            $text = 'Array';
        }

        if (\is_array($var)) {
            $var = var_export($var, 1);
            // $var=$colors->getColoredHTML($var, "green");
            $var = "<pre {$pre_style}>".$var.'</pre>';
            $is_array = true;
        }

        // else {
        //    $var = $colors->getColoredHTML($var, "green");
        // }
        // $text=$colors->getColoredHTML($text);

        $random_id = 'Model_'.substr(md5(rand()), 0, 7);
        $this->model_popup .= Render::html('popup_debug_model', ['MODEL_TITLE' => $text, 'MODEL_BODY' => $var, 'MODEL_ID' => $random_id]);

        $button_html = Render::html('popup_debug_button', ['MODEL_TITLE' => $text, 'MODEL_ID' => $random_id]);
        $this->model_buttons[] = $button_html;
    }

    public function writeModelHtml()
    {
        if (\defined('__DISPLAY_POPUP__')) {
            echo $this->model_popup;
            echo '      <div class="btn-group-vertical">';

            foreach ($this->model_buttons as $k => $html_button) {
                echo $html_button;
            }

            echo '</div>';
        }
    }

    public static function RandomId($prefix = '', $length = 10)
    {
        return $prefix.substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / \strlen($x)))), 1, $length);
    }

    public static function Random($length = 10)
    {
        self::$Random = self::RandomId('', $length);
    }
}
