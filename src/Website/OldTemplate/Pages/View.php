<?php

namespace CWP\Template\Pages;

use CWP\Template\Rain;

class View extends Rain
{
    public static function formerButton($former)
    {
        return Rain::drawTpl(
            'former_button',
            'content',
            $former
        );

    }
    public static function SheetList($html)
    {

        return Rain::drawTpl(
            'sheet_list',
            'content',
            $html
        );

    }

    public static function SheetLink($name, $url, $class, $style, $disabled)
    {

        $sheetLinkParams =
        [
            'SHEET_DISABLED' => $disabled,
            'BUTTON_STYLE' => 'style="' . $style . '"',
            'SHEET_CLASS' => $class,
        ];

        if(!is_array($name) && !is_array($url)) {
            $name = array($name);
            $url = array($url);
        }

        foreach($name as $i => $value) {
            $sheetLinkURLS[] = [
                    'PAGE_FORM_NUMBER' => $name[$i],
                    'PAGE_FORM_URL' => $url[$i],
            ];
        }

        return Rain::drawTpl(
            'sheet_link',
            ['sheetLinks','sheetLinksParams'],
            [$sheetLinkURLS,$sheetLinkParams]
        );
    }

    public static function FormButton($name, $url, $disabled)
    {

        $params = [ 'DISABLED' => $disabled,'NAME' => $name,'URL' => $url,];

        return Rain::drawTpl('form_button', 'params', $params);

    }

    public static function FormButtonList($html)
    {

        return Rain::drawTpl('form_list', 'Form_Buttons', $html);
    }



}
