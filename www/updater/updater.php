<?php
use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaProgramUpdate;
use CWP\Media\Update\AppUpdate;

/**
 * CWP Media tool.
 */
require '../.config.inc.php';

define('TITLE', 'Media Updater');
include_once __LAYOUT_HEADER__;

if (false !== AppUpdate::$UPDATES_PENDING) {
    if (key_exists('update', $_POST)) {
        $mediaUpdates->getUpdateFiles();
        $mediaUpdates->doUpdates();

        echo HTMLDisplay::JavaRefresh('/index.php', 0);
        ob_flush();
    } else {
        echo 'There are '.AppUpdate::$UPDATES_PENDING.' Pending <br>';
        ?>
<form action="/updater/updater.php" method="post">
    <input type="hidden" name="update" value="1">
    <button type="submit" name="submit" class="btn active">Update!</button>
</form>
<?php

    }
} else {
    echo 'All up to date';
    echo HTMLDisplay::JavaRefresh('/index.php', 3);
}

include_once __LAYOUT_FOOTER__;
?>