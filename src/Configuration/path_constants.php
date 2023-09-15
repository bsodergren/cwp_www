<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Utils\MediaDevice;
use Nette\Utils\FileSystem;

define('__PDF_UPLOAD_DIR__', \DIRECTORY_SEPARATOR.'pdf');
define('__ZIP_FILE_DIR__', \DIRECTORY_SEPARATOR.'zip');
define('__XLSX_DIRECTORY__', \DIRECTORY_SEPARATOR.'xlsx');
define('__EMAIL_PDF_UPLOAD_DIR__', \DIRECTORY_SEPARATOR.'uploads');

define('__LAYOUT_DIR__', \DIRECTORY_SEPARATOR.'assets');
define('__LAYOUT_ROOT__', __HTTP_ROOT__.__LAYOUT_DIR__);
define('__THEME_DIR__', __LAYOUT_ROOT__.\DIRECTORY_SEPARATOR.'themes');

define('__UPDATE_CACHE_DIR__', FileSystem::normalizePath(__PUBLIC_ROOT__.'/cache'));
define('__UPDATE_CURRENT_FILE__', FileSystem::normalizePath(__PUBLIC_ROOT__.'/current.txt'));

(new MediaDevice())->run();
define('__DEVICE__', MediaDevice::$DEVICE);
