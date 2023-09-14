<?php
$ClassName = ucfirst(strtolower(__DEVICE__));
$className = 'CWP\\HTML\\'.$ClassName .'\\Footer';
if (class_exists($className))
{
    $className::Display();
}

