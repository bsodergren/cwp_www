<?php
/**
 * CWP Media tool for load flags
 */

use Camoo\Config\Config;
use CWP\Core\Bootstrap;
use CWP\Updater\MediaAppUpdater;

define('__PROJECT_ROOT__', dirname(__FILE__, 4));
define('__PUBLIC_ROOT__', dirname(__FILE__, 3));
define('__HTTP_ROOT__', dirname(__FILE__, 2));

define('__COMPOSER_DIR__', __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'vendor');
require __COMPOSER_DIR__.\DIRECTORY_SEPARATOR.'autoload.php';

$boot      = new Bootstrap(new Config(__PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'config.ini'));

$appUpdate = new MediaAppUpdater();

echo $appUpdate->isUpdate();
