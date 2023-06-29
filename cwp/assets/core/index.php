<?php
require_once('.config.inc.php');

use Nette\Utils\FileSystem;


if (key_exists('update_job', $_REQUEST)) {


    $job_number = $_REQUEST['job_number'];

    if (strlen($job_number) != 6)
    {
        MediaError::msg("warning","There was a problem <br> the job number was incorrect");
    }

    $media->delete_xlsx();
    $media->delete_zip();


    $mediaLoc = new MediaFileSystem($media->pdf_file, $job_number);
    $mediaLoc->getDirectory();

    try {
        if (filesystem::rename($media->base_dir, $mediaLoc->directory) == false) {
            throw new Nette\IOException;
        }
    } catch (Nette\IOException $e) {
        $msg = $e->getMessage();
    }

    if ($msg == '') {
        $media->update_job_number($job_number);
        echo HTMLDisplay::JavaRefresh("/index.php", 0);
    } else {
        MediaError::msg("warning","There was a problem <br> " . $msg,15);
    }
    exit;
}

foreach ($_REQUEST as $key => $value) {

    switch ($key) {
        case  "email_zip":
            define('REFRESH_URL', '/mail.php?job_id=' . $job_id);
            break;
        case  "process":
            define('REFRESH_URL', '/form.php?job_id=' . $job_id);
            break;
            case  "view_xlsx":
            define('REFRESH_URL', '/view.php?job_id=' . $job_id);
            break;
        case  "create_xlsx":
            define('REFRESH_TIMEOUT', 3);
            include_once __LAYOUT_HEADER__;
            $media->excelArray();
            $excel = new MediaXLSX($media);
            ob_flush();
            define('REFRESH_MSG', 'XLSX Files Created');
            break;
        case  "create_zip":
            $xlsx_dir = $media->xlsx_directory;
            $zip_file =  $media->zip_file;
            new Zip($xlsx_dir, $job_id, $zip_file);
            define('REFRESH_MSG', 'ZIP File Created');

            break;

        case  "refresh_import":
            define('REFRESH_TIMEOUT', 3);
            $media->delete_xlsx();
            $media->delete_zip();
            $media->delete_form();
            include_once __LAYOUT_HEADER__;

            new MediaImport($media->pdf_fullname, $media->job_number);
            define('REFRESH_MSG', 'PDF Reimported');

            break;
        case  "delete_zip":
            $media->delete_zip();
            define('REFRESH_MSG', 'Zip Deleted');

            break;
        case  "delete_xlsx":
            $media->delete_xlsx();
            $media->delete_zip();
            define('REFRESH_MSG', 'XLSX and Zip Deleted');
            break;
        case  "delete_job":
            define('REFRESH_URL', "/delete_job.php?job_id=" . $job_id);
            
            break;
    }
}


if (!defined("REFRESH_URL")) {
    define('REFRESH_URL', '/index.php');
}
