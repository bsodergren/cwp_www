<?php
require_once(".config.inc.php");

define('REFRESH_TIMEOUT', 3);
include_once __LAYOUT_HEADER__;
$form_number = $_REQUEST['form_number'];;
$media->excelArray($form_number);
$excel = new MediaXLSX($media,true);

echo HTMLDisplay::JavaRefresh("/form.php?".$_SERVER['QUERY_STRING'], 0);