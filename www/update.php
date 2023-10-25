<?php
/**
 * CWP Media tool for load flags
 */

use CWP\HTML\HTMLDisplay;
use CWP\Spreadsheet\Media\MediaXLSX;

/**
 * CWP Media tool.
 */

require_once '.config.inc.php';
define('TITLE', 'Update Form');

HTMLDisplay::$timeout = 3;
use CWP\Utils\MediaDevice;

MediaDevice::getHeader();
$form_number          = $_REQUEST['form_number'];

$media->excelArray($form_number);
$excel                = new MediaXLSX($media, true);

echo HTMLDisplay::JavaRefresh(__URL_PATH__.'/form.php?'.$_SERVER['QUERY_STRING'], 0);
