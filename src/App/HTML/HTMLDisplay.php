<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\HTML;

use CWP\Core\MediaSettings;
use CWP\Template\Template;

/*
 * CWP Media tool
 */

const STREAM_CLASS = 'show test-nowrap px-5 rounded-pill';

\define('MSG_CLASS', 'bg-primary bg-opacity-75 w-75 fs-3 '.STREAM_CLASS);
\define('HEADER_CLASS', 'bg-success bg-opacity-50 w-50 mx-5 fs-6 '.STREAM_CLASS);

class HTMLDisplay
{
    public static $url     = false;

    public static $timeout = 0;

    public static $msg     = '';

    public static $flushdummy;

    public function __construct()
    {
        ob_implicit_flush(true);
        @ob_end_flush();

        $flushdummy       = '';
        for ($i = 0; $i < 1200; $i++) {
            $flushdummy .= '      ';
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
            if (\is_array($msg)) {
                foreach ($msg as $key => $value) {
                    $url_array[] = $key.'='.urlencode($value);
                }
                $url_params = implode('&', $url_array);
                $url        = $url.$sep.$url_params;
            } else {
                $msg = urlencode($msg);
                if (str_contains($url, '?')) {
                    $sep = '&';
                }

                $url = $url.$sep.'msg='.$msg;
            }
        }

        // if ($timeout > 0) {
        //     $timeout = $timeout * 1000;
        //     $update_inv = $timeout / 100;
        //     Template::echo('progress_bar', ['SPEED' => $update_inv]);
        // }

        echo Template::GetHTML('js_refresh_window', ['_URL' => $url, '_SECONDS' => $timeout]);
    }

    public static function pushhtml($template, $params = [])
    {
        $params['MSG_CLASS']    = MSG_CLASS;
        $params['HEADER_CLASS'] = HEADER_CLASS;
        $contents               = Template::GetHTML($template, $params);
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
        $relativePath = substr($excel_file, \strlen(__HTTP_ROOT__) + 1);
        $url          = __URL_HOME__.'/'.str_replace('\\', '/', $relativePath);

        if (false == self::is_404($url)) {
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

        $url          = __URL_HOME__.'/'.str_replace('\\', '/', $relativePath);

        if (false == self::is_404($url)) {
            return false;
        }

        $url          = 'onclick="event.stopPropagation(); OpenNewWindow(\''.$url.'\')"';

        return $url;
    }

    public static function is_404($url)
    {
        $url = str_replace(' ', '%20', $url);
        file_get_contents($url);
        if (\array_key_exists('4', $http_response_header)) {
            return true;
        }

        return false;
    }

    public function display_table_rows($array, $letter)
    {
        $html             = '';
        $start            = '';
        $end              = '';
        $row_template     = new Template();

        foreach ($array as $part) {
            if ('' == $start) {
                $start = $part['id'];
            }

            $end         = $part['id'];

            $check_front = '';
            $check_back  = '';

            $classFront  = 'Front'.$letter;
            $classBack   = 'Back'.$letter;

            if ('Back' == $part['former']) {
                $check_back = 'checked';
            }
            if ('Front' == $part['former']) {
                $check_front = 'checked';
            }
            $radio_check = '';

            if ('4pg' == $part['config']) {
                $value       = [
                    'Front' => ['value' => 'Front', 'checked' => $check_front, 'text' => 'Front', 'class' => $classFront],
                    'Back'  => ['value' => 'Back', 'checked' => $check_back, 'text' => 'Back', 'class' => $classBack],
                ];
                $radio_check = $this->draw_radio('former_'.$part['id'], $value);
            }

            $facetrim    = MediaSettings::isFacetrim($part);

            $array       = [
                'MARKET'      => $part['market'],
                'PUBLICATION' => $part['pub'],
                'COUNT'       => $part['count'],
                'SHIP'        => $part['ship'],
                'RADIO_BTNS'  => $radio_check,
                'FACE_TRIM'   => $this->draw_checkbox('facetrim_'.$part['id'], $facetrim, 'Face Trim'),
                //  'NO_TRIM'     => $this->draw_checkbox('nobindery_'.$part['id'], $nobindery, 'No Trimmers'),
            ];

            $row_template->template('form/row', $array);
        }

        $AllCheckBoxFront = 'all'.$classFront;
        $AllCheckBoxBack  = 'all'.$classBack;
        if ($end > $start + 1 && '' != $radio_check) {
            $radio_check_array = [
                'LETTER'           => $letter,
                'ALLCHECKBOXFRONT' => $AllCheckBoxFront,
                'ALLCHECKBOXBACK'  => $AllCheckBoxBack,
                'CLASSFRONT'       => $classFront,
                'CLASSBACK'        => $classBack,
            ];
            $row_template->template('form/all_parts', $radio_check_array);
        }

        $html             = $row_template->return();

        return $html;
    }
}
