<?php
/**
 * CWP Media tool
 */

namespace CWP\HTML;

use CWP\Media\MediaSettings;
use Sinergi\BrowserDetector\Browser;

/*
 * CWP Media tool
 */

class HTMLDisplay
{
    public static $url = false;

    public static $timeout = 0;

    public static $msg = '';

    public static $flushdummy;

    public function __construct()
    {
        ob_implicit_flush(true);
        ob_end_flush();

        $flushdummy = '';
        for ($i = 0; $i < 1200; ++$i) {
            $flushdummy = $flushdummy.'      ';
        }
        self::$flushdummy = $flushdummy;
    }

    public static function javaRefresh($url, $timeout = 0, $msg = '')
    {
        $url = str_replace(__URL_PATH__, '', $url);
        $url = __URL_PATH__.'/'.$url;
        $url = str_replace('//', '/', $url);

        if ('' != $msg) {
            $sep = '?';
            $msg = urlencode($msg);
            if (str_contains($url, '?')) {
                $sep = '&';
            }

            $url = $url.$sep.'msg='.$msg;
        }

        if ($timeout > 0) {
            $timeout = $timeout * 1000;
            $update_inv = $timeout / 100;
            Template::echo('progress_bar', ['SPEED' => $update_inv]);
        }

        echo Template::GetHTML('js_refresh_window', ['_URL' => $url, '_SECONDS' => $timeout]);
    }

    public static function pushhtml($template, $params = [])
    {
        $contents = Template::GetHTML($template, $params);
        self::push($contents);
    }

    public static function push($contents)
    {
        echo $contents, self::$flushdummy;
        flush();
        @ob_flush();
    }

    public static function put($contents, $color = null)
    {
        if (null !== $color) {
            $colorObj = new Colors();
            $contents = $colorObj->getColoredSpan($contents, $color);
        }
        self::push($contents."<br> \n");
    }

    public static function echo($value, $exit = 0)
    {
        echo '<pre>'.var_export($value, 1).'</pre>';

        if (1 == $exit) {
            exit;
        }
    }

    public static function output($var)
    {
        self::put($var);
    }

    public function draw_checkbox($name, $value, $text = 'Face Trim', $template = 'elements/checkbox')
    {
        return HTMLForms::draw_checkbox($name, $value, $text, $template);
    }

    public function draw_radio($name, $value)
    {
        return HTMLForms::draw_radio($name, $value);
    }

    public static function draw_hidden($name, $value)
    {
        return HTMLForms::draw_hidden($name, $value);
    }

    public static function draw_excelLink($excel_file)
    {
        $browser = new Browser();

        $relativePath = substr($excel_file, strlen(__HTTP_ROOT__) + 1);
        $url = __URL_HOME__.'/'.str_replace('\\', '/', $relativePath);

        if (false == self::is_404($url)) {
            return false;
        }
        if ('57.0.2987.98' == $browser->getVersion()) {
            return false;

        }

        return 'ms-excel:ofe|u|'.$url;
    }

    public static function getPdfLink($pdf_file)
    {
        $relativePath = substr($pdf_file, strlen(__HTTP_ROOT__) + 1);

        $url = __URL_HOME__.'/'.str_replace('\\', '/', $relativePath);

        if (false == self::is_404($url)) {
            return false;
        }

        $url = 'onclick="event.stopPropagation(); OpenNewWindow(\''.$url.'\')"';

        return $url;
    }

    public static function is_404($url)
    {
        $url = str_replace(' ', '%20', $url);
        file_get_contents($url);
        if (key_exists('4', $http_response_header)) {
            return true;
        }

        return false;
    }
}
