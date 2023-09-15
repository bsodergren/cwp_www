<?php
/**
 * CWP Media tool
 */

use CWP\Media\Media;
use CWP\Media\MediaSetup;
use CWP\Media\Update\DbUpdate;
use CWP\Media\Update\MediaAppUpdater;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Database\Connection;
use Nette\Database\Conventions\DiscoveredConventions;
use Nette\Database\Explorer;
use Nette\Database\Structure;

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
