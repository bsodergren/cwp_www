<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Core\Media;
use PHLAK\Stash;
use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;

$template = new Template();
$HTMLDisplay = new HTMLDisplay();
if (array_key_exists('job_id', $_REQUEST)) {
    $job_id = $_REQUEST['job_id'];
    $job = Media::$connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
    $media = new Media($job);
}

if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
    apache_setenv('dont-vary', '1');
}

$stash = Stash\Cache::file(function (): void {
    $this->setCacheDir(__CACHE_DIR__);
});
Media::$Stash = $stash;
