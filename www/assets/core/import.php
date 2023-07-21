<?php
/**
 * CWP Media tool
 */

require_once '.config.inc.php';

use CWP\exec;
use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaImport;
use Nette\Utils\FileSystem;
use CWP\Media\MediaFileSystem;

HTMLDisplay::$timeout  = 10;
HTMLDisplay::$url      = 'import.php';

$error                 = false;
// Store errors here
$fileExtensionsAllowed = ['pdf'];
// These will be the only file extensions allowed
if (isset($_POST['submit'])) {
    if ('' != $_POST['job_number']) {
        $job_number = $_POST['job_number'];
    }

    if (isset($_POST['mail_job_number'])) {
        $job_number = $_POST['mail_job_number'];
    }

    if (isset($_POST['mail_file'])) {
        list($fullFile, $imap_id) = explode('|', $_POST['mail_file']);

        $fileName                 = basename($fullFile);
        $fileSize                 = filesize($fullFile);
        $fileTmpName              = $fullFile;

        $imap                     = imap_open(__IMAP_HOST__.__IMAP_FOLDER__, __IMAP_USER__, __IMAP_PASSWD__);
    } else {
        $fileName    = $_FILES['the_file']['name'];
        $fileSize    = $_FILES['the_file']['size'];
        $fileTmpName = $_FILES['the_file']['tmp_name'];
    }

    if ('' == $fileName) {
        HTMLDisplay::put("<span class='p-3 text-danger'> no File selected </span> ");
        $error = true;
    } else {
        $f             = explode('.', $fileName);
        $f             = end($f);
        $fileExtension = strtolower($f);
    }

    if (!in_array($fileExtension, $fileExtensionsAllowed)) {
        HTMLDisplay::put('This file extension is not allowed. Please upload a PDF file');
        $error = true;
    }

    if ($fileSize > 4000000000) {
        HTMLDisplay::put('File exceeds maximum size (40MB)');
        $error = true;
    }

    if ('' == $job_number) {
        HTMLDisplay::put("<span class='p-3 text-danger'>No Job Number </span> ");
        $error = true;
    }
    HTMLDisplay::$url = 'import.php';

    if (false == $error) {
        HTMLDisplay::$url     = 'index.php';
        HTMLDisplay::$timeout = 1;

        $media_closing        = '/'.basename($fileName, '.pdf');
        $locations            = new MediaFileSystem($fileName, $job_number);
        $pdf_directory        = $locations->getDirectory('pdf', true);
        $pdf_file             = $pdf_directory.'/'.basename($fileName);

        if (file_exists($pdf_file)) {
            FileSystem::delete($pdf_file);
        }

        if (!file_exists($pdf_file)) {
            $didUpload = move_uploaded_file($fileTmpName, $pdf_file);

            if (false === $didUpload) {
                $didUpload = rename($fileTmpName, $pdf_file);
            }

            if ($didUpload) {
                $qdf_cmd        = FileSystem::normalizePath(__ROOT_BIN_DIR__.'/qpdf');
                $pdf_file       = FileSystem::normalizePath($pdf_file);
                HTMLDisplay::put('Waiting for PDF for finish');

                $process        = new exec($qdf_cmd);
                $process->option($pdf_file);
                $process->option('--pages', '.');
                $process->option('1-z', '--');
                $process->option('--replace-input');
                $process->run();

                //  sleep(5);

                HTMLDisplay::put('The file '.basename($fileName).' has been uploaded');
                HTMLDisplay::put('Job number '.$job_number.'');

                if (isset($imap)) {
                    imap_setflag_full($imap, $imap_id, '\\Seen');
                }
            } else {
                HTMLDisplay::put('An error occurred. Please contact the administrator.');
            } // end if
        } else {
            HTMLDisplay::put('File already was uploaded');
        } // end if

        $MediaImport          = new MediaImport($pdf_file, $job_number);

        if ($MediaImport->status < 1) {
            HTMLDisplay::put("<span class='p-3 text-danger'>File failed to process</span>");
            HTMLDisplay::put("<span class='p-3 text-danger'>Will have to run Refresh Import </span>");
            HTMLDisplay::put(' Click on <a href="'.__URL_PATH__.'/index.php">Home</a> to Continue ');
        }
    }
}
