<?php

class MediaSettings
{

    public static function isTrue($define_name)
    {
        if (defined($define_name)) {



            if (constant($define_name) == true) {
                //  MediaUpdate::echo(constant($define_name));
                return 1;
            }
        }
        return 0;
    }

    public static function isSet($define_name)
    {
        if (defined($define_name)) {
            return 1;
        }
        return 0;
    }
    public static function  jsonString_to_TextForm($string)
    {

        $value_text = '';
        $value_array = json_decode($string, 1);
        if (is_array($value_array)) {
            foreach ($value_array as $text => $link) {
                if (is_array($link)) {
                    $value_text .= $text . " => [,\n";
                    foreach ($link as $text2 => $link2) {
                        $value_text .= "\t $text2 => $link2,\n";
                    }
                    $value_text .= "],\n";
                    continue;
                }
                $value_text .= "$text => $link,\n";
            }
        }
        return $value_text;
    }

    public static function save_post_asJson($string)
    {

        $arr = explode(",", $string);
        $arr2 = [];
        $step = false;
        $nav_array = [];

        foreach ($arr as $k => $string) {

            if (str_contains($string, "]")) {
                $step = false;
                $dropdown_key = '';
                continue;
            }

            if (str_contains($string, "=>")) {
                list($v_key, $value) = explode("=>", $string);
                $value = trim($value);
                $v_key  = trim($v_key);
                
                if (str_contains($value, "[")) {
                    $step = true;
                    $dropdown_key = $v_key;
                    $nav_array[$dropdown_key] = [];
                    continue;
                }

                if ($step == true) {
                    $nav_array[$dropdown_key][$v_key] = $value;
                    continue;
                }

                $arr2[$v_key] = $value;
            }
        }
        $array =  array_merge($arr2, $nav_array);
        //$arr2['dropdown'] = $nav_array;
        return json_encode($array);
    }
}
