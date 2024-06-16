<?php

namespace  CWPDisplay\Template\Callbacks;

use  CWPDisplay\Template\Functions\Functions;

class FunctionCallback
{
    public const FUNCTION_CALLBACK = '|{{function=([a-zA-Z_:]+)\|?(.*)?}}|i';
    public const SCRIPTINCLUDE_CALLBACK = '|{{(scriptinclude)=([a-zA-Z-_/\.]+)\|?([a-zA-Z=$,.\?\{\}]+)?}}|i';

    public function callback_parse_function($matches)
    {
        $helper = new Functions();
        $method = $matches[1];

        // $value = Helper::$method();
        // if(method_exists($helper,$method)){
        return $helper->{$method}($matches);

        // }
    }

    public function callback_script_include($matches)
    {
        $helper = new Functions();
        $method = $matches[1];
        // $value = Helper::$method();
        // if(method_exists($helper,$method)){
        //  return $helper->{$method}($matches);
        // }
    }
}
