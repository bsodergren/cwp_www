<?php
/**
 * CWP Media tool.
 */

use CWP\HTML\HTMLDisplay;
use CWP\HTML\Template;
use CWP\Media\Media;
use CWP\Utils\MediaDevice;

(new MediaDevice())->run();
define('__DEVICE__', MediaDevice::$DEVICE);

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
