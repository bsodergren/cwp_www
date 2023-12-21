<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Utils\Zip;
use CWP\Core\MediaError;
use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaExport;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use Nette\Utils\FileSystem;
use CWP\Core\MediaStopWatch;
use CWP\Media\Import\PDFImport;
use CWP\Filesystem\MediaFileSystem;
use CWP\Spreadsheet\Media\MediaXLSX;
use CWP\Filesystem\Driver\MediaGoogleDrive;

class Index extends MediaProcess
{
    public function run($req)
    {


        if(array_key_exists('submit', $req)) {
            $method = key($req['submit']);
            $this->$method();

        } elseif(array_key_exists('update_job', $req)) {
            $method = $req['update_job'];
            $this->$method($req['job_number']);
        } else {
            dd($req);
        }


    }

    public function addforms()
    {
        $this->url = '/create/addForm.php?job_id=' . $this->job_id;
    }
    public function email_zip()
    {
        $this->url = '/mail.php?job_id=' . $this->job_id;
    }

    public function process()
    {
        $this->url = '/form.php?job_id=' . $this->job_id;
    }

    public function view_xlsx()
    {
        $this->url = '/view.php?job_id=' . $this->job_id;
    }
    public function update_xlsx()
    {
        \define('TITLE', 'Updating Excel files');

        MediaDevice::$NAVBAR = false;
        MediaDevice::getHeader();
        Template::echo('stream/start_page', ['PAGE_LOAD' => template::GetHTML('/stream/page_load', [])]);
        HTMLDisplay::pushhtml('stream/excel/msg', ['TEXT' => 'Updating Workbooks']);
        MediaStopWatch::lap("Create Excel");
        $forms = $this->media->getFormUpdates($this->media->job_id);
        foreach($forms as $f) {
            $this->media->excelArray($f->form_number);
            $excel     = new MediaXLSX($this->media);
            $excel->writeWorkbooks();
        }
        Template::echo('stream/end_page', ['PAGE_CLOSE' => template::GetHTML('/stream/page_close', [])]);
        $this->msg = 'XLSX Files Created';
        MediaDevice::$NAVBAR = true;

    }

    public function create_xlsx()
    {
        \define('TITLE', 'Writing Excel files');

        MediaDevice::$NAVBAR = false;
        MediaDevice::getHeader();
        Template::echo('stream/start_page', ['PAGE_LOAD' => template::GetHTML('/stream/page_load', [])]);
        HTMLDisplay::pushhtml('stream/excel/msg', ['TEXT' => 'Creating Workbooks']);
        MediaStopWatch::lap("Create Excel");
        $this->media->excelArray();
        MediaStopWatch::lap("Excel Array");

        $excel     = new MediaXLSX($this->media);
        MediaStopWatch::lap("Excel Object");
        $excel->writeWorkbooks();
        Template::echo('stream/end_page', ['PAGE_CLOSE' => template::GetHTML('/stream/page_close', [])]);

        $this->msg = 'XLSX Files Created';
        MediaDevice::$NAVBAR = true;
    }

    public function create_zip()
    {

        $zip       = new Zip($this);
        $this->msg = $zip->zip();
        // $msg ='ZIP File Created';
    }

    public function refresh_import()
    {
        $this->timeout = 1;
        if ($msg = null === $this->media->delete_xlsx()) {
            if ($msg = null === $this->media->delete_zip()) {
                $this->media->delete_form();

                \define('TITLE', 'Reimporting Media Drop');
                MediaDevice::getHeader();

                Template::echo('stream/start_page', []);

                $import    = new PDFImport();
                $import->reImport($this->media->pdf_file, $this->media->job_number);

                $this->msg = 'PDF Reimported';
                Template::echo('stream/end_page', []);
            }
        }
    }

    public function export_job()
    {
        $this->media->excelArray();
        $export = new MediaExport($this->media);
        $export->exportZip();
    }



    public function upload()
    {

        \define('TITLE', 'Uploading to Google Drive');
        MediaDevice::getHeader();
        Template::echo('stream/start_page', []);

        $google = new MediaGoogleDrive();
        $mediaLoc = new MediaFileSystem($this->media->pdf_file, $this->media->job_number);
        $mediaLoc->getDirectory();
        $excelDir = $mediaLoc->directory . DIRECTORY_SEPARATOR . "xlsx";
        $basePath = dirname($mediaLoc->directory, 2);
        $filePath = str_replace($basePath, "", $mediaLoc->directory());// . DIRECTORY_SEPARATOR . "xlsx";
        $google->createFolder($filePath);
        HTMLDisplay::pushhtml('stream/excel/msg', ['TEXT' => 'Created DIR ' . $filePath]);

        $files = $mediaLoc->getContents($excelDir, '*.xlsx');

        foreach($files as $filename) {
            $remoteFilename = basename($filename);
            HTMLDisplay::pushhtml('stream/excel/file_msg', ['TEXT' => 'Uploading ' . $remoteFilename]);
            $uploadFilename = $filePath . DIRECTORY_SEPARATOR . $remoteFilename;
            $google->UploadFile($filename, $uploadFilename);
        }


        $this->msg = 'Files Uploaded to google drive';
        Template::echo('stream/end_page', []);
    }




    public function delete_zip()
    {
        if ($msg = null === $this->media->delete_zip()) {
            $this->msg = 'Zip Deleted';
        }
    }

    public function delete_xlsx()
    {
        $msg       = $this->media->delete_xlsx();
        //                $this->media->deleteSlipSheets();
        $msg       = $this->media->delete_zip();
        $this->msg = 'Zip and excel files removed';
    }

    public function delete_job()
    {
        $this->url = '/delete_job.php?job_id=' . $this->job_id;
    }

    public function update_job($job_number)
    {
        $google = new MediaGoogleDrive();

        if (6 != \strlen($job_number)) {
            MediaError::msg('warning', 'There was a problem <br> the job number was incorrect');
        }

        $this->media->delete_xlsx();
        $this->media->delete_zip();

        $mediaLoc = new MediaFileSystem($this->media->pdf_file, $this->media->job_number);
        $mediaLoc->getDirectory();
        //$excelDir = $mediaLoc->directory . DIRECTORY_SEPARATOR . "xlsx";
        $basePath = dirname($mediaLoc->directory, 2);
        $filePath = str_replace($basePath, "", $mediaLoc->directory());// . DIRECTORY_SEPARATOR . "xlsx";
        $google->delete(dirname($filePath, 1));

        // if ($msg = null ===) {
        //     if ($msg = null === $this->media->delete_zip()) {
        // $mediaLoc = new MediaFileSystem($this->media->pdf_file, $job_number);
        // $mediaLoc->getDirectory();

        //dump(posix_getpwuid(getmyuid()));


        $olddir = dirname($this->media->base_dir, 1);
        //     $newdir = dirname($mediaLoc->directory,1);
        //    $msg= FileSystem::makeWritable(dirname($olddir,1));
        //    $msg2= FileSystem::makeWritable($newdir);
        //     dd($msg,$msg2);

        $msg = FileSystem::delete($olddir);
        //     if ($msg = null === $mediaLoc->rename($this->media->base_dir, $mediaLoc->directory)) {
        $this->media->update_job_number($job_number);
        echo HTMLDisplay::JavaRefresh('/index.php', 0);
        // }
        // dd($msg);
        //     }
        // }
        MediaError::msg('warning', 'There was a problem <br> ' . $msg, 15);
        exit;
    }
}
