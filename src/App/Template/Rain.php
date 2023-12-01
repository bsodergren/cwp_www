<?php
namespace CWP\Template;

use Rain\Tpl;
use CWP\Core\Media;

class Rain extends Tpl{



    protected static function drawTpl($template, $varName, $varValue)
    {

        $Tpl = new Tpl();

        if(!is_array($varName)){
            $varName = array($varName);
            if( is_array($varValue)) {

                $varValue[] = $varValue;
            }
        }

        if( !is_array($varValue)) {
            $varValue = array($varValue);
        }

        foreach($varName as $i => $value) {
            $Tpl->assign($value, $varValue[$i]);
        }

        return $Tpl->draw($template, true);

    }




}