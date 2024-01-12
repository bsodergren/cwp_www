<?php
/**
 * CWP Media Load Flag Creator.
 */

use CWP\Utils\MediaDevice;
use Nette\Utils\FileSystem;

define('__LAYOUT_DIR__', \DIRECTORY_SEPARATOR.'assets');
define('__LAYOUT_ROOT__', __HTTP_ROOT__.__LAYOUT_DIR__);
define('__THEME_DIR__', __LAYOUT_ROOT__.\DIRECTORY_SEPARATOR.'themes');

define('__UPDATE_CURRENT_FILE__', FileSystem::normalizePath(__PUBLIC_ROOT__.'/current.txt'));
define('__XLSX_DIRECTORY__', 'xlsx');
define('__ZIP_DIRECTORY__', 'zip');
(new MediaDevice())->run();
define('__DEVICE__', MediaDevice::$DEVICE);
