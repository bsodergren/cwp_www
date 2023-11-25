<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Updater\MediaAppUpdater;

define('__PROJECT_ROOT__', dirname(__FILE__, 4));
define('__PUBLIC_ROOT__', dirname(__FILE__, 3));
define('__HTTP_ROOT__', dirname(__FILE__, 2));

require __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'bootstrap.php';

$appUpdate = new MediaAppUpdater();

echo $appUpdate->isUpdate();
