<?php
/**
 * CWP Media Load Flag Creator
 */

require '../.config.inc.php';

use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;

define('TITLE', 'Media Updater');
use CWP\Utils\MediaDevice;

$timeout = 10;
if (null === Media::$VersionUpdate) {
    $timeout = 5;
}

MediaDevice::getHeader();
echo HTMLDisplay::ProgressBar($timeout);

if (null === Media::$VersionUpdate) {
    HTMLDisplay::put('All up  to date', 'Red');
} else {
    Media::$MediaAppUpdater->getUpdate();
    Media::$MediaAppUpdater->composerUpdate();
}

echo HTMLDisplay::JavaRefresh('/index.php', $timeout);
MediaDevice::getFooter();
