<?php

$ClassName = ucfirst(strtolower(__DEVICE__));
$className = 'CWP\\HTML\\'.$ClassName .'\\Header';
if (class_exists($className))
{
    $className::Display();
}
