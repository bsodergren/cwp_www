<?php
/**
 * CWP Media tool
 */

use CWP\Core\Media;
use CWP\Core\MediaSetup;
use CWP\Updater\DbUpdate;
use CWP\Utils\MediaDevice;
use Nette\Database\Explorer;
use Nette\Database\Structure;
use Nette\Database\Connection;
use CWP\Updater\MediaAppUpdater;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Database\Conventions\DiscoveredConventions;

new MediaSetup();

$connection = new Connection(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);
$storage = new DevNullStorage();
$structure = new Structure($connection, $storage);
$conventions = new DiscoveredConventions($structure);
$explorer = new Explorer($connection, $structure, $conventions, $storage);

Media::$connection = $connection;
Media::$explorer = $explorer;

$appUpdate = new MediaAppUpdater();
Media::$VersionUpdate = $appUpdate->isUpdate();
Media::$CurrentVersion = $appUpdate->current;
Media::$MediaAppUpdater = $appUpdate;

( new DbUpdate() )->checkDbUpdates();

