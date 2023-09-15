<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;

$template = new Template();

if (array_key_exists('job_id', $_REQUEST)) {
    $job_id = $_REQUEST['job_id'];
    $job = Media::$connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
    $media = new Media($job);
}
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
    apache_setenv('dont-vary', '1');
}

// Cache (optional but recommended)
$cache = new \CWP\Cache\File(__UPDATE_CACHE_DIR__);

// new HTMLDisplay();
