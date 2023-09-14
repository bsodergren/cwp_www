<?php
/**
 * CWP Media tool
 */

namespace CWP\HTML;

class Footer
{
    public static function Display()
    {
        $ClassName = ucfirst(strtolower(__DEVICE__));
        $className = 'CWP\\HTML\\'.$ClassName.'\\Footer';
        if (class_exists($className)) {
            $className::Display();
        }
    }
}
