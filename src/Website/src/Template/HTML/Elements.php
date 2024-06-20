<?php
namespace CWPDisplay\Template\HTML;

use CWP\Core\MediaSettings;
use UTMTemplate\HTML\Elements as UTMElements;
use CWPDisplay\Template\Render;

class Elements extends UTMElements
{
    public static $ElementsDir = 'elements/html';

    public static function formInput($template, $params)
    {
        $template = str_replace(self::$ElementsDir,'',$template);
        return Render::html(self::$ElementsDir.'/'.$template.'/input', $params);
    }

    public static function draw_select($name, $text, $options = [], $style = null, $NoOption = false)
    {
        $option_html = '';
        if (false !== $NoOption) {
            $option_html = Render::html(self::$ElementsDir.'/select/option', ['OPTION_VALUE' => null, 'OPTION_TEXT' => $NoOption]);
        }

        if (\count($options) > 0) {
            foreach ($options as $optiontext => $value) {
                $option_html .= Render::html(self::$ElementsDir.'/select/option', ['OPTION_VALUE' => $value, 'OPTION_TEXT' => $optiontext]);
            }
        } else {
            $option_html = Render::html(self::$ElementsDir.'/select/option', ['OPTION_VALUE' => null, 'OPTION_TEXT' => 'No Options']);
        }
        if (null !== $style) {
            $style = 'style="'.$style.'"';
        }

        return Render::html(self::$ElementsDir.'/select/select', ['SELECT_NAME' => $name, 'SELECT_LABEL' => $text, 'SELECT_STYLE' => $style, 'SELECT_OPTIONS' => $option_html]);
    }

    public static function draw_checkbox($name, $value, $text = 'Face Trim', $template = '/checkbox')
    {

        $template =  self::$ElementsDir.$template;
        global $pub_keywords;

        $checked = '';

        $current_value = $value;

        if (1 == $current_value) {
            $checked = 'checked';
        }

        $params = [
            'NAME' => $name,
            'TEXT' => $text,
            'CHECKED' => $checked,
            'VALUE' => $value,
        ];

        return self::formInput($template, $params);
    }

    public static function draw_radio($name, $value)
    {
        $html = '';

        foreach ($value as $option) {
            $params = [
                'NAME' => $name,
                'VALUE' => $option['value'],
                'TEXT' => $option['text'],
                'CLASS' => $option['class'],
                'CHECKED' => $option['checked'],
            ];

            $html .= self::formInput(self::$ElementsDir.'/radio', $params);
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
            $array_key = 'TEXT_'.strtoupper($key);
            if ('label' == $key) {
                $params[$array_key] = 'aria-label="'.$options['label'].'"';
                continue;
            }
            $params[$array_key] = $key.'="'.$options[$key].'"';
        }

        return Render::html(self::$ElementsDir.'/text/text', $params);
    }
    public static function display_table_rows($array, $letter)
    {
        $html = '';
        $start = '';
        $end = '';
        $row_template = '';
        foreach ($array as $part) {
            if ('' == $start) {
                $start = $part['id'];
            }

            $end = $part['id'];

            $check_front = '';
            $check_back = '';

            $classFront = 'Front'.$letter;
            $classBack = 'Back'.$letter;

            if ('Back' == $part['former']) {
                $check_back = 'checked';
            }
            if ('Front' == $part['former']) {
                $check_front = 'checked';
            }
            $radio_check = '';

            if ('4pg' == $part['config']) {
                $value = [
                    'Front' => ['value' => 'Front', 'checked' => $check_front, 'text' => 'Front', 'class' => $classFront],
                    'Back' => ['value' => 'Back', 'checked' => $check_back, 'text' => 'Back', 'class' => $classBack],
                ];
                $radio_check = self::draw_radio('former_'.$part['id'], $value);
            }

            $facetrim = MediaSettings::isFacetrim($part);

            $array = [
                'MARKET' => $part['market'],
                'PUBLICATION' => $part['pub'],
                'COUNT' => $part['count'],
                'SHIP' => $part['ship'],
                'RADIO_BTNS' => $radio_check,
                'FACE_TRIM' => self::draw_checkbox('facetrim_'.$part['id'], $facetrim, 'Face Trim'),
                //  'NO_TRIM'     => $this->draw_checkbox('nobindery_'.$part['id'], $nobindery, 'No Trimmers'),
            ];

            $row_template .= Render::html('pages/form/row', $array);
        }

        // }

        return $row_template;
    }

}