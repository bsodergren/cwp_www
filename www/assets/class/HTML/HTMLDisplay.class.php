<?php
use coderofsalvation\BrowserStream;

class HTMLDisplay
{
    public static $url = false;

    public static $timeout = 0;

    public static $msg = '';

    public static function javaRefresh($url, $timeout = 0, $msg = '')
    {
        $url = str_replace(__URL_PATH__, '', $url);
        $url = __URL_PATH__.'/'.$url;
        $url = str_replace('//', '/', $url);

        if ($msg != '') {
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


public static function put($contents,$color=null)
{

    $colorObj = new colors();
    $contents = $colorObj->getColoredSpan($contents, $color);
    BrowserStream::put($contents . "<br> \n");
}


    public static function echo($value, $exit = 0)
    {
        echo '<pre>'.var_export($value, 1).'</pre>';

        if ($exit == 1) {
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

        $rootPath = $conf['server']['root_dir'].$conf['server']['web_root'];
        $relativePath = substr($excel_file, strlen($rootPath) + 1);
        $url = __URL_HOME__.'/'.str_replace('\\', '/', $relativePath);

        return 'ms-excel:ofe|u|'.$url;
    }

    public static function getPdfLink($pdf_file)
    {
        global $conf;

        $rootPath = $conf['server']['root_dir'].$conf['server']['web_root'];
        $relativePath = substr($pdf_file, strlen($rootPath) + 1);
        $url = __URL_HOME__.'/'.str_replace('\\', '/', $relativePath);

        return $url;
    }

}


class Colors
{
    private $foreground_colors = [];

    private $background_colors = [];

    private $fg_color;

    public function __construct()
    {
        // Set up shell colors
        $this->foreground_colors['black']        = '0;30';
        $this->foreground_colors['dark_gray']    = '1;30';
        $this->foreground_colors['blue']         = '0;34';
        $this->foreground_colors['light_blue']   = '1;34';
        $this->foreground_colors['green']        = '0;32';
        $this->foreground_colors['light_green']  = '1;32';
        $this->foreground_colors['cyan']         = '0;36';
        $this->foreground_colors['light_cyan']   = '1;36';
        $this->foreground_colors['red']          = '0;31';
        $this->foreground_colors['light_red']    = '1;31';
        $this->foreground_colors['purple']       = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown']        = '0;33';
        $this->foreground_colors['yellow']       = '1;33';
        $this->foreground_colors['light_gray']   = '0;37';
        $this->foreground_colors['white']        = '1;37';

        $this->background_colors['black']      = '40';
        $this->background_colors['red']        = '41';
        $this->background_colors['green']      = '42';
        $this->background_colors['yellow']     = '43';
        $this->background_colors['blue']       = '44';
        $this->background_colors['magenta']    = '45';
        $this->background_colors['cyan']       = '46';
        $this->background_colors['light_gray'] = '47';
    } //end __construct()

    public function getClassColor()
    {
        if (isset($this->foreground_colors[$this->fg_color])) {
            return 'color:' . $this->fg_color . ';';
        }
        return '';
    }

    public function getColoredDiv($html, $background_color)
    {

        $class_tag = '';
        if (isset($this->background_colors[$background_color])) {
            $class_tag = "class";
        }
    }

    // Returns colored string
    public function getColoredSpan($string, $foreground_color = null, $background_color = null)
    {
        $this->fg_color = $foreground_color;
        $colored_string = '<span style="' . $this->getClassColor() . '">' . $string . '</span>';

        return $colored_string;
    } //end getColoredHTML()


    public function getColoredString($string, $foreground_color = null, $background_color = null)
    {
        $colored_string = '';

        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . 'm';
        }

        // Check if given background color found
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . 'm';
        }

        // Add string and end coloring
        $colored_string .= $string . "\033[0m";

        return $colored_string;
    } //end getColoredString()


    // Returns all foreground color names
    public function getForegroundColors()
    {
        return array_keys($this->foreground_colors);
    } //end getForegroundColors()


    // Returns all background color names
    public function getBackgroundColors()
    {
        return array_keys($this->background_colors);
    } //end getBackgroundColors()


} //end class
