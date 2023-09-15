<?php

require '../.config.inc.php';

use CWP\Media\Media;
use CWP\HTML\HTMLDisplay;

define('TITLE', 'Media Updater');
include_once __LAYOUT_HEADER__;


echo Media::$VersionUpdate;
if(Media::$VersionUpdate !== null){
    Media::$MediaAppUpdater->getUpdate();
    Media::$MediaAppUpdater->composerUpdate();

}

// echo HTMLDisplay::JavaRefresh('/index.php', 3);
include_once __LAYOUT_FOOTER__;
