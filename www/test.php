<?php
/**
 * CWP Media Load Flag Creator

 */

use CWP\Core\Media;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use CWP\Core\MediaStopWatch;

require_once '.config.inc.php';

// dd($localFile, $RemoteFile);

MediaDevice::getHeader();

function getValue($array,$value)
{
    if('' != $array[$value]){
        return $array[$value];
    }
    return null;
}
$array = ['var' => "stuff"];
if($job = getValue($array,"var"))  {
    $foo = $job;
}

dump($foo);
$job = null;
$array = ['var' => ""];
if($job = getValue($array,"var"))  {
    $foo = $job;
}


dump($foo);

MediaDevice::getFooter();
