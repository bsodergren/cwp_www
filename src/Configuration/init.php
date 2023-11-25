<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;

$template = new Template();
$HTMLDisplay = new HTMLDisplay();

if (array_key_exists('job_id', $_REQUEST))
{
    $job_id = $_REQUEST['job_id'];

    $media = Media::get("job_id_".$job_id,5,function() use ($job_id) {
        $job = Media::$connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
        return new Media($job);
    });
}

if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
    apache_setenv('dont-vary', '1');
}

