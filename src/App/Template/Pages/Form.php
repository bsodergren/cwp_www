<?php

namespace CWP\Template\Pages;

use CWP\Template\Rain;

class Form extends Rain
{




    public static function FormButtons($name, $url, $class, $style, $disabled)
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
}