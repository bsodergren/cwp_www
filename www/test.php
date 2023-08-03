<?php
/**
 * CWP Media tool
 */

use CWP\Media\Import\OrigPDFImport;
use CWP\Media\Import\PDFImport;
use CWP\Media\Media;
use Fpdi\FPDI;
use Fpdi\fpdi_pdf_parser ;

/**
 * CWP Media tool.
 */
$origpdf = 'D:\development\cwp_app\public\www\files\media\pdf\0323-C_RunSheets_Itasca-orig.pdf';
$newpdf = 'D:\development\cwp_app\public\www\files\media\pdf\0323-C_RunSheets_Itasca.pdf';

require_once '.config.inc.php';
define('TITLE', 'Form Editor');

include_once __LAYOUT_HEADER__;
/*
$pdf = new fpdi_pdf_parser($newpdf);
$pdf->setPageNo(1);
dd($pdf->getContent());

// get the page count
$pageCount = $pdf->setSourceFile($newpdf);

// iterate through all pages

for ($pageNo = 1; $pageNo <= $pageCount; ++$pageNo) {
    // import a page
    $templateId = $pdf->importPage($pageNo);
    dd(get_class_methods($pdf));
}
*/

$procpdf = new PDFImport();
$procpdf->processPdf($newpdf, 1, 1);
//$procpdf = new OrigPDFImport();
//$procpdf->processPdf($origpdf, 1, 1);
// dd($procpdf->form);
// RUN SHEETS
