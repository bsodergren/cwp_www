<?php
/**
 * CWP Media tool
 */

require_once '.config.inc.php';

$form_number      = $_REQUEST['form_number'];

$media->excelArray($form_number);

$excel            = new MediaXLSX($media);
$excel->writeWorkbooks();

ob_flush();
$msg              = 'XLSX Files Created';

define('REFRESH_MSG', $msg);
HTMLDisplay::$url = '/view.php?job_id='.$job_id.'&form_number='.$form_number;
