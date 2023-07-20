<?php
/**
 * CWP Media tool
 */

use coderofsalvation\BrowserStream;

class HTMLDisplay
{
    public static $url     = false;

    public static $timeout = 0;

    public static $msg     = '';

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
            $timeout    = $timeout * 1000;
            $update_inv = $timeout / 100;
            Template::echo('progress_bar', ['SPEED' => $update_inv]);
        }

        echo Template::GetHTML('js_refresh_window', ['_URL' => $url, '_SECONDS' => $timeout]);
    }

    public static function put($contents, $color = null)
    {
        $colorObj = new colors();
        $contents = $colorObj->getColoredSpan($contents, $color);
        BrowserStream::put($contents."<br> \n");
    }

    public static function echo($value, $exit = 0)
    {
        echo '<pre>'.var_export($value, 1).'</pre>';

        if (1 == $exit) {
            exit;
        }
    }

    public static function output($var, $nl = '')
    {
        //        echo $var.$nl."\n";
        BrowserStream::put($var.$nl);
        //      ob_flush();
    }

    public function draw_checkbox($name, $value, $text = 'Face Trim')
    {
        return HTMLForms::draw_checkbox($name, $value, $text);
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
        global $conf;

        $rootPath     = $conf['server']['root_dir'].$conf['server']['web_root'];
        $relativePath = substr($excel_file, strlen($rootPath) + 1);
        $url          = __URL_HOME__.'/'.str_replace('\\', '/', $relativePath);

        return 'ms-excel:ofe|u|'.$url;
    }

    public static function getPdfLink($pdf_file)
    {
        global $conf;

        $rootPath     = $conf['server']['root_dir'].$conf['server']['web_root'];
        $relativePath = substr($pdf_file, strlen($rootPath) + 1);
        $url          = __URL_HOME__.'/'.str_replace('\\', '/', $relativePath);

        return $url;
    }
}
