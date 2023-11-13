<?php
/**
 * CWP Media Load Flag Creator
 */

require '../.config.inc.php';

use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;

define('TITLE', 'Media Updater');
use CWP\Utils\MediaDevice;

$timeout = 25;
MediaDevice::getHeader();

echo HTMLDisplay::ProgressBar($timeout);

if (null !== Media::$VersionUpdate) {
    Media::$MediaAppUpdater->getUpdate();
    Media::$MediaAppUpdater->composerUpdate();
} else {
    $timeout = 5;
    HTMLDisplay::put('All up  to date', 'Red');
}

// echo HTMLDisplay::JavaRefresh('/index.php', $timeout);
MediaDevice::getFooter();
