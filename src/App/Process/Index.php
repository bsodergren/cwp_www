<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Core\MediaError;
use CWP\Filesystem\MediaFileSystem;
use CWP\HTML\HTMLDisplay;
use CWP\Media\Import\PDFImport;
use CWP\Media\MediaExport;
use CWP\Spreadsheet\Media\MediaXLSX;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use CWP\Utils\Zip;

class Index extends MediaProcess
{
    public function run($req)
    {
        $method = key($req['submit']);

        $this->$method();
    }

    public function addforms()
    {
        $this->url = '/create/addForm.php?job_id='.$this->job_id;
    }
    public function email_zip()
    {
        $this->url = '/mail.php?job_id='.$this->job_id;
    }

    public function process()
    {
        $this->url = '/form.php?job_id='.$this->job_id;
    }

    public function view_xlsx()
    {
        $this->url = '/view.php?job_id='.$this->job_id;
    }

    public function create_xlsx()
    {
        \define('TITLE', 'Writing Excel files');

        MediaDevice::getHeader();
        Template::echo('stream/start_page', []);
        HTMLDisplay::pushhtml('stream/excel/msg', ['TEXT' => 'Creating Workbooks']);
        $this->media->excelArray();

        $excel     = new MediaXLSX($this->media);

        $excel->writeWorkbooks();
        Template::echo('stream/end_page', []);

        $this->msg = 'XLSX Files Created';
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
                $import->reImport($this->media->pdf_fullname, $this->media->job_number);

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
        $this->url = '/delete_job.php?job_id='.$this->job_id;
    }

    public function update_job($job_number)
    {
        if (6 != \strlen($job_number)) {
            MediaError::msg('warning', 'There was a problem <br> the job number was incorrect');
        }

        if ($msg = null === $this->media->delete_xlsx()) {
            if ($msg = null === $this->media->delete_zip()) {
                $mediaLoc = new MediaFileSystem($this->media->pdf_file, $job_number);
                $mediaLoc->getDirectory();
                if ($msg = null === $mediaLoc->rename($this->media->base_dir, $mediaLoc->directory)) {
                    $this->media->update_job_number($job_number);
                    dd($this->media, $mediaLoc);

                    echo HTMLDisplay::JavaRefresh('/index.php', 0);
                }
                dd($msg);
            }
        }
        MediaError::msg('warning', 'There was a problem <br> '.$msg, 15);
        exit;
    }
}
