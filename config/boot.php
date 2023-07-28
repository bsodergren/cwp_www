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

$mediaUpdates     = new AppUpdate($connection);
$mediaUpdates->init();

Media::$connection = $connection;
Media::$explorer = $explorer;

