<?php

use CWP\Media\Media;
use CWP\Media\MediaProgramUpdate;
use CWP\Utils;
use CWP\HTML\Template;
use CWP\Db\MediaDbUpdate;
use Nette\Database\Explorer;
use Nette\Database\Structure;
use Nette\Database\Connection;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Database\Conventions\DiscoveredConventions;
/**
 * CWP Media tool
 */


//$__conf_pathCheck = __PUBLIC_ROOT__.'/configCheck.php';
//$__conf_checked   = __PUBLIC_ROOT__.'/.config.true';
$__test_nav_links = __PUBLIC_ROOT__.'/test_navlinks.php';

/*
if (!file_exists($__conf_pathCheck)) {
    exit('Root path not set correctly');
}

if (!file_exists($__conf_checked)) {
    require_once $__conf_pathCheck;
}
*/

if (file_exists($__test_nav_links)) {
    require_once $__test_nav_links;
} else {
    define('__DEV_LINKS__', []);
}

$refresh       = MediaDbUpdate::createDatabase();

$connection    = new Connection(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);
$storage       = new DevNullStorage();
$structure     = new Structure($connection, $storage);
$conventions   = new DiscoveredConventions($structure);
$explorer      = new Explorer($connection, $structure, $conventions, $storage);

$UpdaterObj    = new MediaDbUpdate($connection);
$refresh = $UpdaterObj->checkDbUpdates($refresh);

unset($UpdaterObj);


if (true == $refresh) {
    header('Location:  '.__URL_PATH__.'/index.php');
   exit;
}

$template       = new Template();
$mediaUpdates   = new MediaProgramUpdate();

if (array_key_exists('job_id', $_REQUEST)) {
    $job_id = $_REQUEST['job_id'];
    $job    = $connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
    $media  = new Media($job);
}

