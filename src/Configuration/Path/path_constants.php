<?php
/**
 * CWP Media Load Flag Creator.
 */

use Nette\Utils\FileSystem;

define('APP_HOME', $boot->Config['server']['url_root']);

define('__UPDATE_CURRENT_FILE__', FileSystem::normalizePath(__ROOT_DIRECTORY__.'/current.txt'));

define('__XLSX_DIRECTORY__', 'xlsx');
define('__ZIP_DIRECTORY__', 'zip');



