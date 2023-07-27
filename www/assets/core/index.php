<?php
require_once '.config.inc.php';

use CWP\Media\MediaExport;
use CWP\Zip;
use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaError;
use CWP\Media\MediaFileSystem;
use coderofsalvation\BrowserStream;
use CWP\Media\Import\PDFImport;
use CWP\Spreadsheet\Media\MediaXLSX;
use CWP\Spreadsheet\Slipsheets\SlipSheetXLSX;


if (array_key_exists('update_job', $_REQUEST)) {
    $job_number = $_REQUEST['job_number'];

    if (strlen($job_number) != 6) {
        MediaError::msg('warning', 'There was a problem <br> the job number was incorrect');
    }

    if ($msg = $media->delete_xlsx() === null) {
        if ($msg = $media->delete_zip() === null) {
            $mediaLoc = new MediaFileSystem($media->pdf_file, $job_number);
            $mediaLoc->getDirectory();
            if ($msg = MediaFileSystem::rename($media->base_dir, $mediaLoc->directory) === null) {
                $media->update_job_number($job_number);
                dd($media,$mediaLoc);

                echo HTMLDisplay::JavaRefresh('/index.php', 0);
            }
            dd($msg);
        }
    }
    MediaError::msg('warning', 'There was a problem <br> '.$msg, 15);
    exit;
}

foreach ($_REQUEST as $key => $value) {
    switch ($key) {
        case  'email_zip':
            HTMLDisplay::$url = '/mail.php?job_id='.$job_id;
            break;
        case  'process':
            HTMLDisplay::$url = '/form.php?job_id='.$job_id;
            break;
        case  'view_xlsx':
            HTMLDisplay::$url = '/view.php?job_id='.$job_id;
            break;
        case  'create_xlsx':
            include __LAYOUT_HEADER__;
            HTMLDisplay::put("processing for excel ","red");

            BrowserStream::put("Getting array<BR>");

            $media->excelArray();
            BrowserStream::put("Writing new excel files<BR>");

            $excel = new MediaXLSX($media);
            BrowserStream::put("Writing new excel files<BR>");

            $excel->writeWorkbooks();

            $msg = 'XLSX Files Created';
            break;

        case  'create_slip':
            include_once __LAYOUT_HEADER__;

            $media->excelArray();
            $excel = new MediaXLSX($media);
            $excel->writeMasterWorkbook();

            //$slipsheets = new SlipSheetXLSX($media);
            //$slipsheets->CreateSlips();
            ob_flush();
            $msg = 'Slip file Created';
            break;

        case  'create_zip':
            $xlsx_dir = $media->xlsx_directory;
            $zip_file = $media->zip_file;
            $zip = new Zip();
            $msg = $zip->zip($xlsx_dir, $job_id, $zip_file);
            //$msg ='ZIP File Created';

            break;

        case  'refresh_import':
            HTMLDisplay::$timeout = 3;
            if ($msg = $media->delete_xlsx() === null) {
                if ($msg = $media->delete_zip() === null) {
                    $media->delete_form();
                    include_once __LAYOUT_HEADER__;
                    $import              = new PDFImport();
                    $import->Import($media->pdf_fullname, $media->job_number);
                    $msg = 'PDF Reimported';
                }
            }

            break;

            case  'export_job':
                $media->excelArray();
                $export = new MediaExport($media);
                $forms          = $export->exportZip();
                break;

        case  'delete_zip':
            if ($msg = $media->delete_zip() === null) {
                $msg = 'Zip Deleted';
            }

            break;
        case  'delete_xlsx':
            if ($msg = $media->delete_xlsx() === null) {
                //                $media->deleteSlipSheets();
                if ($msg = $media->delete_zip() === null) {
                    $msg = 'Zip and excel files removed';
                }
            }

            break;
        case  'delete_job':
            HTMLDisplay::$url = '/delete_job.php?job_id='.$job_id;

            break;
    }
}

define('REFRESH_MSG', $msg);

if (HTMLDisplay::$url === false) {
    HTMLDisplay::$url = '/index.php';
}
