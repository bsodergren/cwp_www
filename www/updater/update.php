<?php
/**
 * CWP Media tool for load flags
 */

require '../.config.inc.php';

use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;

define('TITLE', 'Media Updater');
use CWP\Utils\MediaDevice;

MediaDevice::getHeader();

echo Media::$VersionUpdate;
if (null !== Media::$VersionUpdate) {
    Media::$MediaAppUpdater->getUpdate();
    Media::$MediaAppUpdater->composerUpdate();
} else {
    echo 'All up  to date';
}

// echo HTMLDisplay::JavaRefresh('/index.php', 3);
MediaDevice::getFooter();
