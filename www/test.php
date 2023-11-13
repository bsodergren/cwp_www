<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Filesystem\MediaFileSystem;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

require_once '.config.inc.php';

//dd($localFile, $RemoteFile);

MediaDevice::getHeader();
$timeout    = 15;
$timeout    = $timeout * 1000;
$update_inv = $timeout / 100;
Template::echo('progress_bar', ['SPEED' => $update_inv]);
MediaDevice::getFooter();
