<?php
/**
 * CWP Media tool
 */

use CWP\Media\Media;
use CWP\HTML\Template;
use CWP\HTML\HTMLDisplay;

$__test_nav_links = __PUBLIC_ROOT__.'/test_navlinks.php';

if (file_exists($__test_nav_links)) {
    require_once $__test_nav_links;
} else {
    define('__DEV_LINKS__', []);
}

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


new HTMLDisplay();
