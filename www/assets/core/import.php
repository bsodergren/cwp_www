<?php 
require_once('.config.inc.php');
use Nette\Utils\FileSystem;

define("REFRESH_TIMEOUT", 5);
$error=false;
// Store errors here
$fileExtensionsAllowed = ['pdf'];
// These will be the only file extensions allowed
if (isset($_FILES['the_file'])) {
  $fileName      = $_FILES['the_file']['name'];
  $fileSize      = $_FILES['the_file']['size'];
  $fileTmpName   = $_FILES['the_file']['tmp_name'];
  $fileType      = $_FILES['the_file']['type'];
  $f             = explode('.', $fileName);
  $f             = end($f);
  $fileExtension = strtolower($f);
  $media_closing = '/' . basename($fileName, '.pdf');
  $locations = new MediaFileSystem($fileName, $_POST['job_number']);
  $pdf_directory = $locations->getDirectory('pdf', true);
  $pdf_file      = $pdf_directory . '/' . basename($fileName);
}

if (isset($_POST['submit'])) {
    if (!in_array($fileExtension, $fileExtensionsAllowed)) {
        HTMLDisplay::output('This file extension is not allowed. Please upload a JPEG or PNG file');
        define("REFRESH_URL", 'import.php');
        $error=true;
    }

    if ($fileSize > 4000000000) {
        HTMLDisplay::output('File exceeds maximum size (40MB)');
        define("REFRESH_URL", 'import.php');
        $error=true;
    }

    if ($error== false) {
        define("REFRESH_URL", 'index.php');

        if (file_exists($pdf_file)) {
            FileSystem::delete($pdf_file);
        }

        if (!file_exists($pdf_file)) {
            $didUpload = move_uploaded_file($fileTmpName, $pdf_file);


            if ($didUpload) {
                $descriptorspec = [
                  0 => ['pipe', 'r',],
                  // stdin is a pipe that the child will read from
                  1 => ['pipe', 'w',],
                ];

                $qdf_cmd = FileSystem::normalizePath('"' . __ROOT_BIN_DIR__ . '/qpdf" ');
                $pdf_file = FileSystem::normalizePath($pdf_file);
                $cmd = $qdf_cmd . '"' . $pdf_file . '" ' . ' --pages . 1-z -- --replace-input ';

                $process = proc_open($cmd, $descriptorspec, $pipes);

                HTMLDisplay::output('Waiting for PDF for finish');
                sleep(5);            

                HTMLDisplay::output('The file ' . basename($fileName) . ' has been uploaded <br>');
                HTMLDisplay::output('Job number ' . $_POST['job_number'] . '<br>');
            } else {
                HTMLDisplay::output('An error occurred. Please contact the administrator.');
            } //end if
        } else {
            HTMLDisplay::output("File already was uploaded<br>\n");
        } //end if

        $val = $explorer->table('media_job')->where('pdf_file', $pdf_file)->select('job_id');
        foreach ($val as $u) {
            $job_id = $u->job_id;
        }

        $job_id = '';
        if ($job_id == '') {
            $MediaImport = new MediaImport($pdf_file, $_POST['job_number']);

            if ($MediaImport->status < 1) {
                HTMLDisplay::output("<span class='p-3 text-danger'>File failed to process</span> <br>");
                HTMLDisplay::output("<span class='p-3 text-danger'>Will have to run Refresh Import </span><br>");
                HTMLDisplay::output(' Click on <a href="' . __URL_PATH__ . '/index.php">Home</a> to Continue <br>');
            }
        }
    }
}




