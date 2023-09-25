<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem;

/*
 * CWP Media tool
 */

use CWP\Core\Media;
use CWP\Core\Bootstrap;
use Nette\Utils\FileSystem;
use CWP\Filesystem\Driver\MediaLocal;
use CWP\Filesystem\Driver\MediaDropbox;

class MediaFileSystem
{
    public $directory;

    public object $fileDriver;

    public $job_number;

    public $pdf_file;

    public function __construct($pdf_file = null, $job_number = null)
    {
        $this->job_number = $job_number;
        $this->pdf_file = $pdf_file;
        if (Media::$Dropbox) {
            define('__FILES_DIR__', '');
            $this->fileDriver = new MediaDropbox();
        } else {

            if (array_key_exists('media_files', Bootstrap::$CONFIG['server'])) {
                if (true == Bootstrap::$CONFIG['server']['media_files']) {
                    define('__FILES_DIR__', __HTTP_ROOT__.Bootstrap::$CONFIG['server']['media_files']);
                }
            }
            $this->fileDriver = new MediaLocal();
        }
    }

    public function getContents($path)
    {
        return $this->fileDriver->getContents($path);
    }

    public function exists($file)
    {
        return $this->fileDriver->exists($file);
    }

    public function uploadFile($filename, $dropboxFilename, $options = [])
    {
        return $this->fileDriver->UploadFile($filename, $dropboxFilename, $options);
    }

    public function DownloadFile($filename)
    {
        return $this->fileDriver->DownloadFile($filename);
    }

    public function createFolder($path)
    {
        return $this->fileDriver->createFolder($path);
    }

    public function delete($file)
    {
        return $this->fileDriver->delete($file);
    }

    public function rename($old, $new)
    {
        return $this->fileDriver->rename($old, $new);
    }
    public function save($filename,$path)
    {
        return $this->fileDriver->save($filename,$path);
    }

    public function getFilename($type = '', $form_number = '', $create_dir = false)
    {
        return $this->filename($type, $form_number, $create_dir);
    }

    public function getDirectory($type = '', $create_dir = true)
    {
        return $this->directory($type, $create_dir);
    }

    public function filename($type = '', $form_number = '', $create_dir = false)
    {
        $directory = '';

        if (!isset($this->pdf_file)) {
            return false;
        }

        $file = basename($this->pdf_file, '.pdf');
        $filename = $this->job_number.'_'.$file;

        $type = strtolower($type);
        switch ($type) {
            case 'xlsx':
                $filename = $filename.'_FM'.$form_number.'.xlsx';
                break;
            case 'slips':
                $filename = $filename.'_CountSlips_FM'.$form_number.'.xlsx';
                break;
            case 'zip':
                if ('' != $form_number) {
                    $filename = $filename.'_FM'.$form_number.'.zip';
                } else {
                    $filename .= '.zip';
                }
                break;
            case 'pdf':
                $filename = $this->pdf_file;
                break;
        }

        if ('' != $type) {
            $directory = $this->directory($type, $create_dir);
        }

        $filename = $directory.\DIRECTORY_SEPARATOR.$filename;
        $filename = FileSystem::normalizePath($filename);

        return $filename;
    }

    public function directory($type = '', $create_dir = true)
    {
        $output_filename = '';

        if (false !== $this->filename()) {
            $output_filename = $this->filename();
            $output_filename = str_replace($this->job_number.'_', '', $output_filename);
            $output_filename = $this->job_number.$output_filename;
        }

        $directory = __FILES_DIR__.__MEDIA_FILES_DIR__.\DIRECTORY_SEPARATOR.$output_filename;

        $type = strtolower($type);
        switch ($type) {
            case 'xlsx':
                $directory .= \DIRECTORY_SEPARATOR.__XLSX_DIRECTORY__;

                break;
            case 'zip':
                $directory .= \DIRECTORY_SEPARATOR.__ZIP_DIRECTORY__;
                break;
            case 'upload':
                if (Media::$Dropbox) {
                    $directory = __TEMP_DIR__;
                } else {
                    $directory = __FILES_DIR__.\DIRECTORY_SEPARATOR.'Uploads';
                }
                break;
            case 'pdf':
                $directory = __FILES_DIR__.\DIRECTORY_SEPARATOR.'Uploads';
                break;
        }

        $directory = FileSystem::unixSlashes($directory);
        $this->directory = FileSystem::normalizePath($directory);

        if (true == $create_dir) {
            FileSystem::createDir($directory, 511);
        }

        return $this->directory;
    }
}
