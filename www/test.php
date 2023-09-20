<?php
/**
 * CWP Media tool for load flags
 */

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;

/**
 * CWP Media tool for load flags.
 */

require_once '.config.inc.php';
// https://www.dropbox.com/developers/apps

$app = new DropboxApp(__DROPBOX_APP_KEY__, __DROPBOX_APP_SECRET__, __DROPBOX_AUTH_TOKEN__);
$dropbox = new Dropbox($app);
dd($dropbox->createFolder('/Media/230361_1022-C_Run_Sheets_Itasca/xlsx'));
