<?php
/**
 * CWP Media tool
 */

use CWP\HTML\Template;
use CWP\Media\Media;
use CWP\Media\Update\AppUpdate;
use CWP\Media\Update\DbUpdate;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Database\Connection;
use Nette\Database\Conventions\DiscoveredConventions;
use Nette\Database\Explorer;
use Nette\Database\Structure;

$__test_nav_links = __PUBLIC_ROOT__.'/test_navlinks.php';

if (file_exists($__test_nav_links)) {
    require_once $__test_nav_links;
} else {
    define('__DEV_LINKS__', []);
}
$refresh          = DbUpdate::createDatabase();

$connection       = new Connection(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);
$storage          = new DevNullStorage();
$structure        = new Structure($connection, $storage);
$conventions      = new DiscoveredConventions($structure);
$explorer         = new Explorer($connection, $structure, $conventions, $storage);

$UpdaterObj       = new DbUpdate($connection);
$refresh          = $UpdaterObj->checkDbUpdates($refresh);

unset($UpdaterObj);

if (true == $refresh) {
    header('Location:  '.__URL_PATH__.'/index.php');
    exit;
}

$template         = new Template();
$mediaUpdates     = new AppUpdate($connection);
$mediaUpdates->init();

if (array_key_exists('job_id', $_REQUEST)) {
    $job_id = $_REQUEST['job_id'];
    $job    = $connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
    $media  = new Media($job);
}
