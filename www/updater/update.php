<?php

require '../.config.inc.php';

use CWP\Core\Media;
use CWP\HTML\HTMLDisplay;

define('TITLE', 'Media Updater');
use CWP\Utils\MediaDevice;
MediaDevice::getHeader();


echo Media::$VersionUpdate;
if(Media::$VersionUpdate !== null){
    Media::$MediaAppUpdater->getUpdate();
    Media::$MediaAppUpdater->composerUpdate();

}else {
    echo "All up  to date";
}

// echo HTMLDisplay::JavaRefresh('/index.php', 3);
MediaDevice::getFooter();
