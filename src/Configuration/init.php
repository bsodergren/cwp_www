<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Database\Map\Media_job;


$template = new Template();
$HTMLDisplay = new HTMLDisplay();

if (array_key_exists('job_id', $_REQUEST))
{
    $job_id = $_REQUEST['job_id'];

    $media = Media::get("job_id".$job_id,5,function() use ($job_id) {

        $job = Media::$connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
        //$job = Media_job::where("job_id",$job_id)->getOne();
       // return $job;
        return new Media($job);
    });
    Media::$Obj = $media;
} else {
    Media::$Obj = new Media();;
}

if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
    apache_setenv('dont-vary', '1');
}

