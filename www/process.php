<?php
/**
 * CWP Media Load Flag Creator.
 */

use CWP\Process\MediaProcess;

define('PROCESS', true);

require '.config.inc.php';

MediaProcess::Check($media);
