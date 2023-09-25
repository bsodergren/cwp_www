<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;

/**
 * CWP Media tool for load flags.
 */

require_once '.config.inc.php';
// https://www.dropbox.com/developers/apps

MediaDevice::getHeader();

Template::echo("test/main");
MediaDevice::getFooter();
