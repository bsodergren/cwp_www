<?php
/**
 * CWP Media tool
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\HTML\HTMLDisplay;
use CWP\HTML\Template;
use CWP\Media\Import\PDFImport;
use CWP\Media\MediaExec;
use CWP\Media\MediaFileSystem;
use Nette\Utils\FileSystem;

class Import extends MediaProcess
{
    public $error = false;
    // Store errors here
    public $fileExtensionsAllowed = ['pdf'];

    public $page_end;

    public function header()
    {
        define('TITLE', 'Importing PDF File');
        include __LAYOUT_HEADER__;
        Template::echo('stream/start_page', []);
        $this->page_end = Template::GetHTML('stream/end_page', []);
    }

    public function footer()
    {
        echo $this->page_end;
        include_once __LAYOUT_FOOTER__;
    }

    public function run($req)
    {
        global $_POST;
        global $_FILES;
        $this->header();

        // These will be the only file extensions allowed
        if ('' != $_POST['job_number']) {
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

            $imap = imap_open(__IMAP_HOST__.__IMAP_FOLDER__, __IMAP_USER__, __IMAP_PASSWD__);
        } else {
            $fileName = $_FILES['the_file']['name'];
            $fileSize = $_FILES['the_file']['size'];
            $fileTmpName = $_FILES['the_file']['tmp_name'];
        }

        if ('' == $fileName) {
            HTMLDisplay::put("<span class='p-3 text-danger'> no File selected </span> ");
            $this->error = true;
        } else {
            $f = explode('.', $fileName);
            $f = end($f);
            $fileExtension = strtolower($f);
        }

        if (!in_array($fileExtension, $this->fileExtensionsAllowed)) {
            HTMLDisplay::put('This file extension is not allowed. Please upload a PDF file');
            $this->error = true;
        }

        if ($fileSize > 4000000000) {
            HTMLDisplay::put('File exceeds maximum size (40MB)');
            $this->error = true;
        }

        if ('' == $job_number) {
            HTMLDisplay::put("<span class='p-3 text-danger'>No Job Number </span> ");
            $this->error = true;
        }
        $this->url = 'import.php';

        if (false == $this->error) {
            $this->url = 'index.php';
            $this->timeout = 1;

            $media_closing = '/'.basename($fileName, '.pdf');
            $locations = new MediaFileSystem($fileName, $job_number);
            $pdf_directory = $locations->getDirectory('pdf', true);
            $pdf_file = $pdf_directory.'/'.basename($fileName);

            //        if (file_exists($pdf_file)) {
            //            FileSystem::delete($pdf_file);
            //        }

            if (!file_exists($pdf_file)) {
                $didUpload = move_uploaded_file($fileTmpName, $pdf_file);

                if (false === $didUpload) {
                    $didUpload = rename($fileTmpName, $pdf_file);
                }

                if ($didUpload) {
                    $pdf_file = FileSystem::normalizePath($pdf_file);
                    $process = new MediaExec();
                    $process->cleanPdf($pdf_file);

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

            $MediaImport = new PDFImport();
            $MediaImport->Import($pdf_file, $job_number);

            if ($MediaImport->status < 1) {
                HTMLDisplay::put("<span class='p-3 text-danger'>File failed to process</span>");
                HTMLDisplay::put("<span class='p-3 text-danger'>Will have to run Refresh Import </span>");
                HTMLDisplay::put(' Click on <a href="'.__URL_PATH__.'/index.php">Home</a> to Continue ');
            }
        }
    }
}
