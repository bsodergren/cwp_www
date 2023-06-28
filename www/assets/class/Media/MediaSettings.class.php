<?php

class MediaSettings
{



    

    public static function skipTrimmers($data)
    {
        $publication = $data["pub"];
        $nobindery = $data["nobindery"];

        if($nobindery == 1){
            return 1;
        }

       // foreach()
      if(str_contains( __PDF_NOTRIM__, $publication) ) {
       return 1;
      }
      return 0;


    }

    public static function isFacetrim($data)
    {
        $publication = $data["pub"];
        $facetrim = $data["facetrim"];

        if($facetrim == 1){
            return 1;
        }
       // foreach()
       if($facetrim === null)
       {
            if(str_contains( __PUB_FACETRIM__, $publication) ) {
                return 1;
            }
        }
        return 0;
    }

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
                if(str_contains($string,"{")){
                    $value_text .= "$text => $link,\n";
                } else {
                    $value_text .= "$link\n";
                }
            }
        } 

        return trim($value_text);
    }

    public static function save_post_asJson($setting_str)
    {


        if (str_contains($setting_str, "=>")) {
            $arr = explode(",", $setting_str);
            $arr2 = null;
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
            if ($arr2 === null){
                $arr2 = $arr;
            }

            $array =  array_merge($arr2, $nav_array);
         
            //$arr2['dropdown'] = $nav_array;
        } else {
            $array = explode("\n", $setting_str);
                        $array = array_map('trim',$array);

        }

        return json_encode($array);

    }
}
