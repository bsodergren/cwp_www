<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Core\MediaQPDF;
use CWP\Filesystem\MediaDropbox;
use CWP\Filesystem\MediaFileSystem;
use CWP\HTML\HTMLDisplay;
use CWP\Media\Import\PDFImport;
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
        if ('' != $_POST['dropbox']['pdf_file']) {
            $pdf_file = $_POST['dropbox']['pdf_file'];
            $job_number = $_POST['dropbox']['job_number'];
        }

        if ('' != $_FILES['the_file']['name']) {
            $fileName = $_FILES['the_file']['name'];
            $fileSize = $_FILES['the_file']['size'];
            $fileTmpName = $_FILES['the_file']['tmp_name'];

            $job_number = $_POST['upload']['job_number'];

            $locations = new MediaFileSystem();
            $pdf_directory = $locations->getDropboxDirectory('pdf', false);
            $pdf_file = $pdf_directory.\DIRECTORY_SEPARATOR.basename($fileName);

            MediaQPDF::cleanPDF($fileTmpName);
            MediaDropbox::UploadFile($fileTmpName, $pdf_file, ['autorename' => false]);
            //        if (file_exists($pdf_file)) {
            //            FileSystem::delete($pdf_file);
            //        }
        }

        if ('' == $pdf_file) {
            HTMLDisplay::put("<span class='p-3 text-danger'> no File selected </span> ");
            $this->error = true;
        } else {
            $f = explode('.', $pdf_file);
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
