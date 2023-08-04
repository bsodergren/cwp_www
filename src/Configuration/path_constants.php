<?php

use Nette\Utils\FileSystem;


define('__PDF_UPLOAD_DIR__', DIRECTORY_SEPARATOR.'pdf');
define('__ZIP_FILE_DIR__', DIRECTORY_SEPARATOR.'zip');
define('__XLSX_DIRECTORY__', DIRECTORY_SEPARATOR.'xlsx');
define('__EMAIL_PDF_UPLOAD_DIR__', DIRECTORY_SEPARATOR.'uploads');



 define('__LAYOUT_DIR__', DIRECTORY_SEPARATOR.'assets');
 define('__LAYOUT_ROOT__', __HTTP_ROOT__.__LAYOUT_DIR__);
 define('__TEMPLATE_DIR__', __LAYOUT_ROOT__.DIRECTORY_SEPARATOR.'template');

 define('__LAYOUT_HEADER__', __LAYOUT_ROOT__.DIRECTORY_SEPARATOR.'header.php');
 define('__LAYOUT_NAVBAR__', __LAYOUT_ROOT__.DIRECTORY_SEPARATOR.'navbar.php');
 define('__LAYOUT_FOOTER__', __LAYOUT_ROOT__.DIRECTORY_SEPARATOR.'footer.php');

 define('__UPDATE_URL__', 'https://raw.githubusercontent.com/bsodergren/cwp_www/main/AppUpdates');

 define('__UPDATE_TMP_DIR__',FileSystem::normalizePath(__PUBLIC_ROOT__.'/temp'));
 define('__UPDATE_CACHE_DIR__',FileSystem::normalizePath(__PUBLIC_ROOT__.'/cache'));
 define('__UPDATE_CURRENT_FILE__',FileSystem::normalizePath(__PUBLIC_ROOT__.'/current.txt'));
 define('__UPDATE_LOG_FILE__',FileSystem::normalizePath(__PUBLIC_ROOT__.'/update.log'));