<?php

$delete_file = __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'delete.txt';
if(file_exists($delete_file))
{
$file = file_get_contents($delete_file);

dump($file);

}