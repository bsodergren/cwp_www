<?php
/**
 * CWP Media Load Flag Creator.
 */

use CWP\Core\Media;
use CWP\Core\MediaSetup;
use CWP\Database\Database;
use CWP\Updater\DbUpdate;
use CWP\Updater\MediaAppUpdater;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Database\Connection;
use Nette\Database\Conventions\DiscoveredConventions;
use Nette\Database\Explorer;
use Nette\Database\Structure;
$connection = new Connection(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);
$storage = new DevNullStorage();
$structure = new Structure($connection, $storage);
$conventions = new DiscoveredConventions($structure);
$explorer = new Explorer($connection, $structure, $conventions, $storage);

Media::$connection = $connection;
Media::$explorer = $explorer;
Media::$MySQL = new Database();

new MediaSetup();

$appUpdate = new MediaAppUpdater();

Media::$VersionUpdate = $appUpdate->isUpdate();

Media::$CurrentVersion = $appUpdate->current;
Media::$MediaAppUpdater = $appUpdate;

( new DbUpdate() )->checkDbUpdates();
