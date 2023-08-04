<?php
/**
 * CWP Media tool
 */

use CWP\AutoUpdate\AutoUpdate;
use CWP\HTML\HTMLDisplay;
use CWP\HTML\Template;
use CWP\Media\Media;
use Monolog\Logger;

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

define('__UPDATE_CURRENT_VER__',trim(file_get_contents(__UPDATE_CURRENT_FILE__)));
Media::$AutoUpdate = new AutoUpdate(__UPDATE_TMP_DIR__, __PUBLIC_ROOT__, 60);
Media::$AutoUpdate->setCurrentVersion(__UPDATE_CURRENT_VER__);
Media::$AutoUpdate->setUpdateUrl(__UPDATE_URL__);

$logger = new Logger('default');
$logger->pushHandler(new Monolog\Handler\StreamHandler(__UPDATE_LOG_FILE__));
Media::$AutoUpdate->setLogger($logger);

// Cache (optional but recommended)
$cache = new \CWP\Cache\File(__UPDATE_CACHE_DIR__);
Media::$AutoUpdate->setCache($cache, 3600);

new HTMLDisplay();
