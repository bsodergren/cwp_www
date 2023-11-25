<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Template;

use CWP\Core\Media;
use CWP\Utils\MediaDevice;
use CWP\Core\MediaSettings;

/**
 * CWP Media tool.
 */
class Template
{
    public static $static_html;

    public $html           = '';

    public $header_html    = '';

    public $default_params = [];

    public $template;

    private $test          = 0;

    public $error          = true;

    public function __construct()
    {
    }

    public static function getJavascript($template = '', $array = [], $error = true, $cache = true)
    {


        $template_obj        = new self();
        $template_obj->error = $error;

        $template_obj->template($template, $array, true,$cache);

        return $template_obj->html;

    }
    public static function GetHTML($template = '', $array = [], $error = true, $cache = true)
    {

        $template_obj        = new self();
        $template_obj->error = $error;
        $template_obj->template($template, $array,false,$cache);

        return $template_obj->html;
    }

    public static function echo($template = '', $array = [], $error = true, $cache = true)
    {
        $template_obj        = new self();
        $template_obj->error = $error;
        $template_obj->template($template, $array,false,$cache);
        echo $template_obj->html;
    }

    public function callback_replace($matches)
    {
        return '';
    }

    public function clear()
    {
        $this->html = '';
    }

    public function return($template = '', $array = [], $cache = true)
    {
        if ($template) {
            $this->template($template, $array,false,$cache);
        }

        $html = $this->html;
        $this->clear();

        return $html;
    }

    public function render($template = '', $array = [], $cache = true)
    {
        if ($template) {
            $this->template($template, $array,false,$cache);
        }

        $html = $this->html;
        $this->clear();
        echo $html;
    }

    private function loadTemplate($template, $js = false)
    {
        $template_file = MediaDevice::getTemplateFile($template, $js);
        if (null !== $template_file) {
            return file_get_contents($template_file).\PHP_EOL;
        }

        if (true == $this->error) {
            $template_text = '<h1>NO TEMPLATE FOUND<br>';
            $template_text .= 'FOR <pre>'.$template.'</pre></h1> <br>';
        } else {
            $template_text = '';
        }
        //        $template_text = '<!-- END OF '.$template.'-->'.\PHP_EOL;

        return $template_text.\PHP_EOL;
    }

    private function defaults($text)
    {
        preg_match_all('/%%([A-Z_]+)%%/m', $text, $output_array);
        $params               = [];

        foreach ($output_array[1] as $n => $def) {
            if (MediaSettings::isSet($def)) {
                $params[$def] = \constant($def);
            }
        }
        $this->default_params = $params;
    }

    private function parse($text, $params = [])
    {
        $this->defaults($text);
        $params = array_merge($params, $this->default_params);
        if (\is_array($params)) {
            foreach ($params as $key => $value) {
                $key  = '%%'.strtoupper($key).'%%';
                $text = str_replace($key, $value, $text);
            }

            $html = preg_replace_callback('|%%(\w+)%%|i', [$this, 'callback_replace'], $text);
        }

        return $html;
    }

    public function template($template, $params = [], $js = false, $cache=true)
    {

        if($cache === true) {
            $template_name = trim(str_replace(['\\','/'],"-",$template),"-");
            $template_name_params = $template_name."_param";


            $cache_params = Media::$Stash->get($template_name_params);
            if($cache_params === false) {
                Media::$Stash->put($template_name_params,$params,10);
            }

            if(is_array($cache_params) && is_array($params)) {
                $val = array_diff_assoc($params,$cache_params);
                if(count($val) > 0){
                    Media::$Stash->put($template_name_params,$params,10);
                    Media::$Stash->forget($template_name);
                }
            }


            $html = Media::$Stash->get($template_name);

            if($html === false) {
                $template_text = $this->loadTemplate($template, $js);
                $html          = $this->parse($template_text, $params);
                Media::$Stash->put($template_name,$html,10);
            }
        } else {
            $template_text = $this->loadTemplate($template, $js);
            $html          = $this->parse($template_text, $params);
        }

        $this->add($html);

        return $html;
    }

    public function add($var)
    {
        if (\is_object($var)) {
            $this->html .= $var->html;
        } else {
            $this->html .= $var;
        }
    }

    public static function VersionText()
    {
        global $mediaUpdates;

        $installed = '0.0.0';

        $latest    = '0.0.0';

        return [$installed, $latest];
    }
}
