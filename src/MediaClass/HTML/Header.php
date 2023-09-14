<?php
/**
 * CWP Media tool
 */

namespace CWP\HTML;

class Header
{
    public static function Display()
    {
        $ClassName = ucfirst(strtolower(__DEVICE__));
        $className = 'CWP\\HTML\\'.$ClassName.'\\Header';
        if (class_exists($className)) {
            $className::Display();
        }
    }
}
