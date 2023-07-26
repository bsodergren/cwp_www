<?php
namespace CWP\HTML;

use CWP\HTML\Template;
/**
 * CWP Media tool
 */

class HTMLForms
{
    public static function formInput($template, $params)
    {
        return Template::GetHTML($template.'/input', $params);
    }

    public static function draw_select($name, $text, $options = [], $style = null)
    {
        $option_html = '';
        if (count($options) > 0) {
            foreach ($options as $optiontext => $value) {
                $option_html .= Template::GetHTML('elements/select/option', ['OPTION_VALUE' => $value, 'OPTION_TEXT' => $optiontext]);
            }
        } else {
            $option_html = Template::GetHTML('elements/select/option', ['OPTION_VALUE' => null, 'OPTION_TEXT' => 'No Options']);
        }
        if (null !== $style) {
            $style = 'style="'.$style.'"';
        }

        return Template::GetHTML('elements/select/select', ['SELECT_NAME' => $name, 'SELECT_LABEL' => $text, 'SELECT_STYLE' => $style, 'SELECT_OPTIONS' => $option_html]);
    }

    public static function draw_checkbox($name, $value, $text = 'Face Trim',$template='elements/checkbox')
    {
        global $pub_keywords;

        $checked       = '';

        $current_value = $value;

        if (1 == $current_value) {
            $checked = 'checked';
        }

        $params        = [
            'NAME'    => $name,
            'TEXT'    => $text,
            'CHECKED' => $checked,
        ];

        return self::formInput($template, $params);
    }

    public static function draw_radio($name, $value)
    {
        $html = '';

        foreach ($value as $option) {
            $params = [
                'NAME'    => $name,
                'VALUE'   => $option['value'],
                'TEXT'    => $option['text'],
                'CLASS'   => $option['class'],
                'CHECKED' => $option['checked'],
            ];

            $html .= self::formInput('elements/radio', $params);
            // $html .= '<input type="radio" class="' . $option["class"] . '" name="' . $name . '" value="' . $option["value"] . '" ' . $option['checked'] . '>' . $option['text'] . ' '.PHP_EOL;
        }
        // $html = $html . "<br>"."\n";
        return $html;
    }

    public static function draw_hidden($name, $value)
    {
        return '<input type="hidden" name="'.$name.'" value="'.$value.'">'.\PHP_EOL;
    }

    public static function draw_text($name, $options = [])
    {
        $params['TEXT_NAME'] = $name;

        foreach (array_keys($options) as $key) {
            $array_key          = 'TEXT_'.strtoupper($key);
            if ('label' == $key) {
                $params[$array_key] = 'aria-label="'.$options['label'].'"';
                continue;
            }
            $params[$array_key] = $key.'="'.$options[$key].'"';
        }

        return Template::GetHTML('elements/text/text', $params);
    }
}
