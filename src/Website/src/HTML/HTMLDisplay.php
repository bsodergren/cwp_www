<?php
/**
 * CWP Media Load Flag Creator
 */

namespace  CWPDisplay\HTML;

use CWP\Core\MediaSettings;
use  CWPDisplay\Template\Template;

/*
 * CWP Media tool
 */

const STREAM_CLASS = 'show test-nowrap px-5 rounded-pill';

// \define('MSG_CLASS', 'bg-primary bg-opacity-75 w-75 fs-3 '.STREAM_CLASS);
// \define('HEADER_CLASS', 'bg-success bg-opacity-50 w-50 mx-5 fs-6 '.STREAM_CLASS);

\define('MSG_CLASS', 'bg-opacity-75 fs-3 '.STREAM_CLASS);
\define('HEADER_CLASS', 'bg-opacity-50 mx-5 fs-6 '.STREAM_CLASS);

class HTMLDisplay
{
    public static $url = false;

    public static $timeout = 0;

    public static $msg = '';

    public static $flushdummy;
    public static $BarStarted = false;

    public function __construct()
    {
        ob_implicit_flush(true);
        @ob_end_flush();

        $flushdummy = '';
        for ($i = 0; $i < 1200; ++$i) {
            $flushdummy .= '      ';
        }
        self::$flushdummy = $flushdummy;
    }

    public static function ProgressBar($timeout = 5)
    {
        if ('start' == strtolower($timeout)) {
            self::$BarStarted = true;
            self::pushhtml('progress_bar', []);

            return;
        }

        if ($timeout > 0) {
            $timeout *= 1000;
            $update_inv = $timeout / 100;
            if (false == self::$BarStarted) {
                self::pushhtml('progress_bar', []);
                self::$BarStarted = false;
            }

            self::pushhtml('progressbar_js', ['SPEED' => $update_inv]);
        }
    }

    public static function javaRefresh($url, $timeout = 0, $msg = '')
    {
        $url = str_replace(__URL_PATH__, '', $url);
        $url = __URL_PATH__.'/'.$url;
        $url = str_replace('//', '/', $url);

        if ('' != $msg) {
            $sep = '?';
            if (\is_array($msg)) {
                foreach ($msg as $key => $value) {
                    $url_array[] = $key.'='.urlencode($value);
                }
                $url_params = implode('&', $url_array);
                $url = $url.$sep.$url_params;
            } else {
                $msg = urlencode($msg);
                if (str_contains($url, '?')) {
                    $sep = '&';
                }

                $url = $url.$sep.'msg='.$msg;
            }
        }
        echo Template::GetHTML('js_refresh_window', ['_URL' => $url, '_SECONDS' => $timeout]);
    }

    public static function redirect($url, $timeout = 0, $msg = '')
    {
        echo Template::GetHTML('js_refresh_window', ['_URL' => $url, '_SECONDS' => $timeout]);
    }

    public static function pushhtml($template, $params = [])
    {
        $params['MSG_CLASS'] = MSG_CLASS;
        $params['HEADER_CLASS'] = HEADER_CLASS;
        $contents = Template::GetHTML($template, $params);
        self::push($contents);
    }

    public static function push($contents)
    {
        echo $contents, self::$flushdummy;
        flush();
        @ob_flush();
    }

    public static function spinner()
    {
        $contents = '
<div class="text-center">
<div class="spinner-border"
style="width: 10rem; height: 10rem; border-width: 2rem;"
 role="status">
<span class="visually-hidden">Loading...</span>
</div>
</div>';
        self::push($contents."<br> \n");
    }

    public static function put($contents, $color = null, $break = true)
    {
        $nlbr = '';
        if (null !== $color) {
            $colorObj = new Colors();
            $contents = $colorObj->getColoredSpan($contents, $color);
        }
        if (true == $break) {
            $nlbr = '<br>';
        }
        self::push($contents.$nlbr."\n");
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
        $relativePath = substr($excel_file, \strlen(__HTTP_ROOT__) + 1);
        $url = __URL_ROOT__.'/'.str_replace('\\', '/', $relativePath);

        if (true == self::is_404($url)) {
            return false;
        }
        if ('APPLICATION' == __DEVICE__) {
            return false;
        }

        return 'ms-excel:ofe|u|'.$url;
    }

    public static function getPdfLink($pdf_file)
    {
        $relativePath = substr($pdf_file, \strlen(__HTTP_ROOT__) + 1);

        $url = __URL_ROOT__.'/'.str_replace('\\', '/', $relativePath);
        // dd($url, __URL_ROOT__, $relativePath, $pdf_file, __HTTP_ROOT__);
        if (true === self::is_404($url)) {
            return false;
        }

        return 'onclick="event.stopPropagation(); OpenNewWindow(\''.$url.'\')"';
    }

    public static function is_404($url)
    {
        $url = str_replace(' ', '%20', $url);

       @file_get_contents($url);
        if (isset($http_response_header)) {
            if (\array_key_exists('0', $http_response_header)) {
                $response = $http_response_header[0];
                if (str_contains($response, '404')) {
                    return true;
                }
            }
        }

        return false;
    }

}
