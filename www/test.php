<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Filesystem\MediaFileSystem;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;

require_once '.config.inc.php';

$dir        = 'Files/230411/1023-C_Runsheets_Itasca/XLS';
$fs         = new MediaFileSystem();

$r          = $fs->dirExists($dir);

$localfile  = 'D:\development\cwp_app\public\www\files\MediaFolder\230411\1023-C_Runsheets_Itasca\xlsx\230411_1023-C_Runsheets_Itasca_FM1.xlsx';

$remotefile = $dir.'/'.basename($localfile);

$fs->save($localfile, $remotefile);
//$r  = $fs->getContents('/');

//dd($localFile, $RemoteFile);

MediaDevice::getHeader();

MediaDevice::getFooter();
