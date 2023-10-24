<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Template\Template;
use CWP\Utils\MediaDevice;

require_once '.config.inc.php';

MediaDevice::getHeader();

Template::echo('test/main');
MediaDevice::getFooter();
