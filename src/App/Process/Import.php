<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Process;

/*
 * CWP Media tool
 */

use CWP\Core\Media;
use CWP\Core\MediaQPDF;
use CWP\Filesystem\MediaFileSystem;
use CWP\HTML\HTMLDisplay;
use CWP\Media\Import\PDFImport;
use CWP\Media\Mail\EmailDisplay;
use CWP\Template\Template;
use CWP\Utils\MediaDevice;
use Nette\Utils\FileSystem;


class Import extends MediaProcess
{
    public $error = false;

    // Store errors here
    public $fileExtensionsAllowed = ['pdf', 'zip'];

    public $page_end;
    public $extractDir;
    public $pdf_name;

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

    private function isEmpty($array, $cat, $field)
    {
        if ('' != $array[$cat][$field]) {
            return $array[$cat][$field];
        }

        return null;
    }

    public function run($req)
    {
        global $_POST;
        global $_FILES;
        $this->header();
        $job_number = '';

        $locations = new MediaFileSystem();

        if ('' != $_FILES['backup_file']['name']) {
            $this->extractDir = __FILES_DIR__.DIRECTORY_SEPARATOR.'backupDir';

            $backupFile = $locations->postSaveFile($_FILES['backup_file'], false);

            $this->unzip($backupFile);
            $this->doBackup($backupFile);
            $this->url = 'index.php';
            $this->timeout = 500;

            return true;
        }

        // These will be the only file extensions allowed
        if ('' != $_POST['local']['pdf_file']) {
            $pdf_file = $_POST['local']['pdf_file'];
            if ('' != $_POST['local']['job_number']) {
                $job_number = $_POST['local']['job_number'];
            }
        }

        if ('' == $job_number) {
            if ('' != $_POST['upload']['job_number']) {
                $job_number = $_POST['upload']['job_number'];
            }
        }

        if ('' != $_FILES['the_file']['name']) {
            $job_number = $_POST['upload']['job_number'];
        }

        if ('' == $job_number) {
            HTMLDisplay::put("<span class='p-3 text-danger'>No Job Number </span> ");
            $this->error = true;
        }

        if (false == $this->error) {
            if ('' != $_FILES['the_file']['name']) {
                $fileSize = $_FILES['the_file']['size'];
                $pdf_file = $locations->postSaveFile($_FILES['the_file']);
                MediaQPDF::cleanPDF($pdf_file);

                EmailDisplay::addImportedPDF($_FILES['the_file']['name'],$job_number);
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
        }

        $this->url = 'import.php';

        if (false == $this->error) {
            $this->url = 'index.php';
            $this->timeout = 50;

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

    public function doBackup($backupFile)
    {
        $json_file = $this->extractDir.DIRECTORY_SEPARATOR.'backup.json';
        $array = json_decode(file_get_contents($json_file), true);

        $pdf_directory = (new MediaFileSystem())->getDirectory('upload', false);

        Filesystem::copy($this->pdf_file, $pdf_directory.DIRECTORY_SEPARATOR.basename($this->pdf_file));
        $this->pdf_file = $pdf_directory.DIRECTORY_SEPARATOR.basename($this->pdf_file);

        $this->job_id = Media::getJobNumber($this->pdf_file, $array['job_number']);
        if (null === $this->job_id) {
            $this->job_id = Media::insertJobNumber($this->pdf_file, $array['job_number']);
        }

        Media::$explorer->table('media_job')->where( // UPDATEME
            'job_id',
            $this->job_id
        )->update([
            'close' => $array['close'],
        ]);

        foreach ($array['forms'] as $form_number => $values) {
            $form = ['config' => $values['config'],
            'bind' => $values['bind'],
            'count' => $values['count'],
            'product' => $array['close'],
            'job_id' => $this->job_id,
            'form_number' => $form_number, ];
            $res[] = Media::$explorer->table('media_forms')->insert($form);
            foreach ($values['data'] as $k => $form_values) {
                $form_values = array_merge($form_values, ['form_number' => $form_number, 'job_id' => $this->job_id]);
                Media::$explorer->table('form_data')->insert($form_values);
            }
        }

        Filesystem::delete($this->extractDir);
        Filesystem::delete($backupFile);

        $this->msg = 'Imported job?';
    }

    public function unzip($file)
    {
        $locations = new MediaFileSystem();

        $zip = new \ZipArchive();
        if (true === $zip->open($file)) {
            $pdf_name = basename($zip->getNameIndex(0), '.pdf');
            $this->pdf_name = $zip->getNameIndex(0);
            $this->extractDir = $this->extractDir.DIRECTORY_SEPARATOR.$pdf_name;
            $this->extractDir = FileSystem::platformSlashes($this->extractDir);

            $locations->createFolder($this->extractDir);
            $zip->extractTo($this->extractDir);
            $zip->close();
            $this->pdf_file = $this->extractDir.DIRECTORY_SEPARATOR.$this->pdf_name;
        }
    }
}
