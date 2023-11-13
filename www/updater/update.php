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
Media::$VersionUpdate = '1';
echo HTMLDisplay::ProgressBar('start');

if (null === Media::$VersionUpdate) {
    HTMLDisplay::put('All up  to date', 'Red');
    $timeout = 2;
} else {
    $timeout = 10;
    // Media::$MediaAppUpdater->getUpdate();
    // Media::$MediaAppUpdater->composerUpdate();
}
echo HTMLDisplay::ProgressBar($timeout);
echo HTMLDisplay::JavaRefresh('/index.php', $timeout);
MediaDevice::getFooter();
