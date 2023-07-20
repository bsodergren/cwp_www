<?php
/**
 * CWP Media tool
 */

require '../.config.inc.php';

define('TITLE', 'Media Updater');
include_once __LAYOUT_HEADER__;

if (false !== MediaProgramUpdate::$UPDATES_PENDING) {
    dd($mediaUpdates);

    if (array_key_exists('update', $_POST)) {
    }
} else {
    echo 'All uo to date';
}

include_once __LAYOUT_FOOTER__;
