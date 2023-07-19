<?php

require_once '.config.inc.php';

use Nette\Utils\FileSystem;

HTMLDisplay::$timeout = 10;
HTMLDisplay::$url = 'import.php';

$error = false;
// Store errors here
$fileExtensionsAllowed = ['pdf'];
// These will be the only file extensions allowed
if (isset($_POST['submit'])) {
    if ($_POST['job_number'] != '') {
        $job_number = $_POST['job_number'];
    }

    if (isset($_POST['mail_job_number'])) {
        $job_number = $_POST['mail_job_number'];
    }

    if (isset($_POST['mail_file'])) {
        list($fullFile, $imap_id) = explode('|', $_POST['mail_file']);

        $fileName = basename($fullFile);
        $fileSize = filesize($fullFile);
        $fileTmpName = $fullFile;

        $hostname = '{imap.gmail.com:993/imap/ssl}CWP';
        $username = $conf['gmail']['name'];
        $password = $conf['gmail']['password'];

        /* try to connect */
        $imap = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: '.imap_last_error());
    } else {
        $fileName = $_FILES['the_file']['name'];
        $fileSize = $_FILES['the_file']['size'];
        $fileTmpName = $_FILES['the_file']['tmp_name'];
    }

    if ($fileName == '') {
        HTMLDisplay::output("<span class='p-3 text-danger'> no File selected </span> <br>");
        $error = true;
    } else {
        $f = explode('.', $fileName);
        $f = end($f);
        $fileExtension = strtolower($f);
    }

    if (! in_array($fileExtension, $fileExtensionsAllowed)) {
        HTMLDisplay::output('This file extension is not allowed. Please upload a PDF file<br>');
        $error = true;
    }

    if ($fileSize > 4000000000) {
        HTMLDisplay::output('File exceeds maximum size (40MB)<br>');
        $error = true;
    }

    if ($job_number == '') {
        HTMLDisplay::output("<span class='p-3 text-danger'>No Job Number </span> <br>");
        $error = true;
    }
    HTMLDisplay::$url = 'import.php';

    if ($error == false) {
        HTMLDisplay::$url = 'index.php';
        HTMLDisplay::$timeout = 5;

        $media_closing = '/'.basename($fileName, '.pdf');
        $locations = new MediaFileSystem($fileName, $job_number);
        $pdf_directory = $locations->getDirectory('pdf', true);
        $pdf_file = $pdf_directory.'/'.basename($fileName);

        if (file_exists($pdf_file)) {
            FileSystem::delete($pdf_file);
        }

        if (! file_exists($pdf_file)) {
            $didUpload = move_uploaded_file($fileTmpName, $pdf_file);

            if ($didUpload === false) {
                $didUpload = rename($fileTmpName, $pdf_file);
            }

            if ($didUpload) {
                $descriptorspec = [
                    0 => ['pipe', 'r'],
                    // stdin is a pipe that the child will read from
                    1 => ['pipe', 'w'],
                ];

                $qdf_cmd = FileSystem::normalizePath('"'.__ROOT_BIN_DIR__.'/qpdf" ');
                $pdf_file = FileSystem::normalizePath($pdf_file);
                $cmd = $qdf_cmd.'"'.$pdf_file.'" '.' --pages . 1-z -- --replace-input ';
                $process = proc_open($cmd, $descriptorspec, $pipes);

                HTMLDisplay::output('Waiting for PDF for finish <br>');
                sleep(5);

                HTMLDisplay::output('The file '.basename($fileName).' has been uploaded <br>');
                HTMLDisplay::output('Job number '.$job_number.'<br>');

                if (isset($imap)) {
                    imap_setflag_full($imap, $imap_id, '\\Seen');
                }
            } else {
                HTMLDisplay::output('An error occurred. Please contact the administrator.');
            } //end if
        } else {
            HTMLDisplay::output("File already was uploaded<br>\n");
        } //end if

        $MediaImport = new MediaImport($pdf_file, $job_number);

        if ($MediaImport->status < 1) {
            HTMLDisplay::output("<span class='p-3 text-danger'>File failed to process</span> <br>");
            HTMLDisplay::output("<span class='p-3 text-danger'>Will have to run Refresh Import </span><br>");
            HTMLDisplay::output(' Click on <a href="'.__URL_PATH__.'/index.php">Home</a> to Continue <br>');
        }
    }
}
