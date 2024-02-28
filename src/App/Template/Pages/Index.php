<?php

namespace CWP\Template\Pages;

use CWP\Template\Rain;

class Index extends Rain
{
   private static $class_create = 'class="btn  btn-success"';
   private static $class_delete = 'class="btn  btn-danger"';
   private static $class_normal = 'class="btn  btn-primary"';

    public static function firstGroup($name, $str='', $text='', $id='', $extra)
    {
        $extra = self::$class_create  . $extra;
        return self::ButtonLink($name, $str, $text, $id, $extra);
    }
    public static function secondGroup($name, $str='', $text='', $id='', $extra)
    {
        $extra = self::$class_normal . $extra;
        return self::ButtonLink($name, $str, $text, $id, $extra);
    }
    public static function deleteGroup($name, $str='', $text='', $id='', $extra)
    {
        $extra = self::$class_delete  . $extra;
        return self::ButtonLink($name, $str, $text, $id, $extra);
    }

    public static function firstGroupLink($url, $text='', $class='', $javascript='')
    {
        $class = self::$class_create  . $class;
        return self::hrefLink($url, $text, $class, $javascript);
    }

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
