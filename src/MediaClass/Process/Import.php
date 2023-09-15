<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Filesystem\MediaFileSystem;
use CWP\HTML\HTMLDisplay;
use CWP\Media\Import\PDFImport;
use CWP\Media\MediaExec;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use Nette\Utils\FileSystem;

class Import extends MediaProcess
{
    public $error = false;
    // Store errors here
    public $fileExtensionsAllowed = ['pdf'];

    public $page_end;

    public function header()
    {
        \define('TITLE', 'Importing PDF File');
        MediaDevice::getHeader();
        Template::echo('stream/start_page', []);
        $this->page_end = Template::GetHTML('stream/end_page', []);
    }

    public function footer()
    {
        echo $this->page_end;
        MediaDevice::getFooter();
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

        if (!isset($job_number)) {
            if (isset($_POST['mail_job_number'])) {
                $job_number = $_POST['mail_job_number'];
            }

            if (isset($_POST['text_job_number'])) {
                if ('' != $_POST['text_job_number']) {
                    $job_number = $_POST['text_job_number'];
                }
            }
        }

        if (isset($_POST['mail_file'])
        && '' == $_FILES['the_file']['name']) {
            list($fullFile, $imap_id) = explode('|', $_POST['mail_file']);

            $location = new MediaFileSystem();
            $fullFile = $location->getDirectory('upload', true).\DIRECTORY_SEPARATOR.$fullFile;

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

        if (!\in_array($fileExtension, $this->fileExtensionsAllowed)) {
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
            $this->timeout = 2;

            // $media_closing = '/'.basename($fileName, '.pdf');
            $locations = new MediaFileSystem($fileName, $job_number);
            $pdf_directory = $locations->getDirectory('pdf', true);

            $pdf_file = $pdf_directory.\DIRECTORY_SEPARATOR.basename($fileName);

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
                } else {
                    HTMLDisplay::put('An error occurred. Please contact the administrator.');
                } // end if
            } else {
                HTMLDisplay::put('File already was uploaded');
            } // end if
            if (isset($imap)) {
                imap_setflag_full($imap, $imap_id, '\\Seen');
            }
            $MediaImport = new PDFImport();
            $MediaImport->Import($pdf_file, $job_number);
            if (0 == $MediaImport->status) {
                HTMLDisplay::put("<span class='p-3 text-danger'>something went wrong</span>");
                HTMLDisplay::put(' Click on <a href="'.__URL_PATH__.'/index.php">Home</a> to Continue ');
            }

            if (2 == $MediaImport->status) {
                HTMLDisplay::put("<span class='p-3 text-danger'>File failed</span>");
                HTMLDisplay::put(' Click on <a href="'.__URL_PATH__.'/index.php">Home</a> to Continue ');
            }
        }
    }
}
