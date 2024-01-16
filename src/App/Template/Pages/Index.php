<?php

namespace CWP\Template\Pages;

use CWP\Template\Rain;

class Index extends Rain
{





    public static function hrefLink($url, $text='', $class='', $javascript='') : string
    {

        $params =
        [

            'URL' =>  $url,
            'CLASS' => $class,
            'JAVA' => $javascript,
            'TEXT' => $text,
        ];


        return Rain::drawTpl(
            'link',
            'params',
            $params
        );
    }



    //submit('submit[view_xlsx]', '', 'view xlsx', '', $class_create.$tooltip.'view_xlsx"');
    public static function ButtonLink($name, $str='', $text='', $id='', $extra)
    {

        if($id == ''){
            $id = 'submit['.$name.']';
        }
        if($text == ''){
            $text = str_replace("_"," ",$name);
            $text = ucwords($text);
        }
        $params =
        [
            'NAME' => 'submit['.$name.']',
            'ID' =>  $id,
            'EXTRA' => $extra,
            'TEXT' => $text,
        ];


        return Rain::drawTpl(
            'submit_button',
            'params',
            $params
        );
    }




}
