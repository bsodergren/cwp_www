<?php
namespace CWP\Core;
use CWP\Core\Media;

class MediaDebug extends Media
{

    public static $DEBUG = false;

    private function trace()
    {
        $trace = debug_backtrace();
        $s     = [];
        $file  = $trace[1]['file'];

        foreach ($trace as $i => $row) {
            $class = '';
            switch ($row['function']) {
                case __FUNCTION__:
                    break;
                case 'dump':
                    $lineno = $row['line'];
                    //break;
                case 'dd':
                    $lineno = $row['line'];
                    break;
                case 'trace':
                case 'require_once':
                case 'include_once':
                case 'require':
                case 'include':
                case '__construct':
                case '__directory':
                case '__filename':
                    case '__dump':
                    break;

                default:

                    if ('' != $row['class']) {
                        $class = $row['class'].$row['type'];
                    }
                    $s[]      = $class.$row['function']."()";
                   // $file   = $row['file'];
                    break;
            }
           // if($i == 5){
           //     break;
           // }
            $i++;
        }
      //  $s = array_reverse($s);
        $s_str = implode("->",$s);
        $file  = pathinfo($file, \PATHINFO_BASENAME);

        return $file.':'.$lineno.':'.$s_str;
    }

    private function __dump(...$content)
    {
        $trace = $this->trace();

        switch(func_num_args())
        {
            case 1:
                if(is_array($content)){
                    foreach($content as $str){
                        if(is_array($str)){
                            foreach($str as $str2){
                                $output[] = $str2;
                            }
                        }else {
                            $output[] = $str;

                        }
                    }
                } else {
                    $output[] = $content;
                }
                break;
                default:
                foreach(func_get_args() as $str){
                    // if(is_array($str)){
                    //     foreach($str as $str2){
                    //         $output[] = $str2;
                    //     }
                    // }else {
                        $output[] = $str;
                    // }
                }
        }

        $output['trace'] = $trace;
        return $output;

    }
    public static function dump(...$content)
    {
        if(self::$DEBUG == false) return null;

        dump((new self)->__dump($content));

    }
    public static function dd(...$content)
    {

        if(self::$DEBUG == false) return null;

        dd((new self)->__dump($content));
    }


}