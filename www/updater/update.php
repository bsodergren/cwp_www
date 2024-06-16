<?php
/**
 * CWP Media Load Flag Creator
 */

require '../.config.inc.php';

use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;

define('TITLE', 'Media Updater');
use CWP\Utils\MediaDevice;

MediaDevice::getHeader();

echo HTMLDisplay::ProgressBar('start');

$timeout = 20;

if (null === Media::$VersionUpdate) {
    $latest = Media::$MediaAppUpdater->getLastest();

    HTMLDisplay::put('Checking for latest update '.$latest, 'Blue');
    HTMLDisplay::put('Checking for Composer Updates', 'Red');
    Media::$MediaAppUpdater->composerUpdate();
} else {
    Media::$MediaAppUpdater->getUpdate();
    Media::$MediaAppUpdater->composerUpdate();
}

HTMLDisplay::put('All up  to date', 'Red');
echo HTMLDisplay::ProgressBar($timeout);

echo Elements::JavaRefresh('/index.php', $timeout);

MediaDevice::getFooter();
