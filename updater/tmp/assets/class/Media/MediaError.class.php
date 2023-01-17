<?php

class MediaError
{

    public static function msg($severity, $msg = "", $refresh = 5)
    {
        $url = "/index.php";
        $timeout = $refresh;

        if(is_array($refresh))
        {
            $timeout = 0;
            if(key_exists('url',$refresh))
            {
                $url = $refresh['url'];
            }

            if(key_exists('timeout',$refresh))
            {
                $timeout = $refresh['timeout'];
            }
        }

        if ($msg != '') {
            include_once __LAYOUT_HEADER__;
            Template::echo("error/" . $severity, ['MSG' => $msg]);
        }

        htmldisplay::javaRefresh($url,$timeout);
        exit;
    }
}
?>