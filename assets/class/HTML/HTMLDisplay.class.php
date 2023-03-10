<?php



class HTMLDisplay
{

    public static function javaRefresh($url, $timeout = 0)
    {

        $url = str_replace(__URL_PATH__,'', $url);
        $url = __URL_PATH__ . '/' . $url;
        $url = str_replace("//", "/", $url);
        
        if ($timeout > 0) {
            $timeout = $timeout * 1000;
            $update_inv =  $timeout / 100;
            Template::echo("progress_bar", ['SPEED' => $update_inv]);
        }
        echo Template::GetHTML('js_refresh_window', ['_URL' => $url, '_SECONDS' => $timeout]);
    }

    public static function echo($value, $exit = 0)
    {

        echo '<pre>' . var_export($value, 1) . '</pre>';

        if ($exit == 1) {
            exit;
        }
    }
    
    public static function output($var,$nl="")
    {
        echo $var . $nl."\n";
        ob_flush();
    }



    public function draw_checkbox($name, $value, $text = 'Face Trim')
    {
        global $pub_keywords;

        $checked = "";


        $current_value = $value;


        if ($current_value == 1) {
            $checked = "checked";
        }

        $html = '';
        $html .= '<input type="checkbox" name="' . $name . '" value="1" ' . $checked . '>' . $text;
        return $html;
    }

    public function draw_radio($name, $value)
    {
        $html = '';

        foreach ($value as $option) {
            $html .= '<input type="radio" class="' . $option["class"] . '" name="' . $name . '" value="' . $option["value"] . '" ' . $option['checked'] . '>' . $option['text'] . '&nbsp;';
        }
        // $html = $html . "<br>"."\n";
        return $html;
    }

    
}
