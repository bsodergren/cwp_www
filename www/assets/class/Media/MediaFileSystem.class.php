<?php
use Nette\Utils\FileSystem;

class MediaFileSystem
{
    public $directory;
    public $job_number;
    public $pdf_file;

    public function __construct($pdf_file=null, $job_number=null)
    {
        $this->job_number = $job_number;
        $this->pdf_file = $pdf_file;
    }

    public function getFilename($type = '', $form_number = '', $create_dir = '')
    {
        return $this->__filename($type, $form_number, $create_dir);
    }

    private function __filename($type = '', $form_number = '', $create_dir = false)
    {
        $directory = '';

        if(!isset($this->pdf_file)) {
            return false;
        }

        $file = basename($this->pdf_file, ".pdf");
        $filename = $this->job_number . '_' . $file;


        if (strtolower($type) == 'xlsx') {

            $filename = $filename . "_FM" . $form_number . '.xlsx';
        }

        if (strtolower($type) == 'zip') {

            if ($form_number != '') {
                $filename =  $filename . "_FM" . $form_number . '.zip';
            } else {
                $filename =  $filename . ".zip";
            }
       }
        if (strtolower($type) == 'pdf') {
            $filename =  $this->pdf_file;

        }

        if($type != '')
        {
             $directory = $this->__directory($type, $create_dir);
        }


        $filename = $directory . DIRECTORY_SEPARATOR  . $filename;
        $filename = FileSystem::normalizePath($filename);
        return $filename;
    }

    private function __directory($type = '', $create_dir = false)
    {
        $output_filename = '';

        if($this->__filename() !== false)
        {
            $output_filename = DIRECTORY_SEPARATOR  . $this->__filename();
        }

        $directory = '';

        if (strtolower($type) == 'xlsx') {
            $directory =  $output_filename . __XLSX_DIRECTORY__;
        }
        if (strtolower($type) == 'pdf') {
            $directory =  $output_filename . __PDF_UPLOAD_DIR__;
        }
        if (strtolower($type) == 'zip') {
            $directory =  $output_filename .  __ZIP_FILE_DIR__;
        }
        
        if (strtolower($type) == 'upload') {
            $directory = __EMAIL_PDF_UPLOAD_DIR__;
        }
        
        $directory = __FILES_DIR__ . $directory;

        $this->directory = FileSystem::normalizePath($directory);

        if ($create_dir == true) {
            FileSystem::createDir($this->directory);
        }

        return $this->directory;
    }

    public function getDirectory($type = '', $create_dir = '')
    {
        return $this->__directory($type, $create_dir);
    }


}


