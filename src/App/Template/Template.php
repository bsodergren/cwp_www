<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Template;

use CWP\Core\MediaSettings;
use CWP\Utils\MediaDevice;

/**
 * CWP Media tool.
 */
class Template
{
    public static $static_html;
    public $html = '';
    public $header_html = '';
    public $default_params = [];
    public $template;
    private $test = 0;
    public $error = true;

    public function __construct()
    {
    }

    public static function GetHTML($template = '', $array = [], $error = true)
    {
        $template_obj = new self();
        $template_obj->error = $error;
        $template_obj->template($template, $array);

        return $template_obj->html;
    }

    public static function echo($template = '', $array = [], $error = true)
    {
        $template_obj = new self();
        $template_obj->error = $error;
        $template_obj->template($template, $array);
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

    public function return($template = '', $array = [])
    {
        if ($template) {
            $this->template($template, $array);
        }

        $html = $this->html;
        $this->clear();

        return $html;
    }

    public function render($template = '', $array = [])
    {
        if ($template) {
            $this->template($template, $array);
        }

        $html = $this->html;
        $this->clear();
        echo $html;
    }

    private function loadTemplate($template)
    {
        $template_file = MediaDevice::getTemplateFile($template);
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
        $params = [];

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
                $key = '%%'.strtoupper($key).'%%';
                $text = str_replace($key, $value, $text);
            }

            $html = preg_replace_callback('|%%(\w+)%%|i', [$this, 'callback_replace'], $text);
        }

        return $html;
    }

    public function template($template, $params = [])
    {
        $template_text = $this->loadTemplate($template);
        $html = $this->parse($template_text, $params);

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

        $latest = '0.0.0';

        return [$installed, $latest];
    }
}
