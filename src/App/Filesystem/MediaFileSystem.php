<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem;

/*
 * CWP Media tool
 */

use CWP\Core\Bootstrap;
use CWP\Core\Media;
use CWP\Filesystem\Driver\MediaDropbox;
use CWP\Filesystem\Driver\MediaGoogleDrive;
use CWP\Filesystem\Driver\MediaLocal;
use Nette\Utils\FileSystem;

class MediaFileSystem
{
    public $directory;

    public object $fileDriver;

    public $job_number;

    public $pdf_file;

    public function __construct($pdf_file = null, $job_number = null)
    {
        $this->job_number = $job_number;
        $this->pdf_file   = $pdf_file;
        $this->fileDriver = Media::getFileDriver();
    }


    public function postSaveFile($postFileArray)
    {
        return $this->fileDriver->postSaveFile($postFileArray);
    }


    public function getContents($path)
    {
        return $this->fileDriver->getContents($path);
    }

    public function dirExists($file)
    {
        return $this->fileDriver->dirExists($file);
    }

    public function exists($file)
    {
        return $this->fileDriver->exists($file);
    }

    public function uploadFile($filename, $remoteFilename, $options = [])
    {
        return $this->fileDriver->UploadFile($filename, $remoteFilename, $options);
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

    public function save($filename, $path)
    {
        return $this->fileDriver->save($filename, $path);
    }

    public function getFilename($type = '', $form_number = '', $create_dir = false)
    {
        return $this->filename($type, $form_number, $create_dir);
    }

    public function getDirectory($type = '', $create_dir = true, $remote = false)
    {
        return $this->directory($type, $create_dir, $remote);
    }

    public function filename($type = '', $form_number = '', $create_dir = false)
    {
        $directory = '';

        if (! isset($this->pdf_file)) {
            return false;
        }

        $file      = basename($this->pdf_file, '.pdf');
        $filename  = $this->job_number.'_'.$file;
        $type      = strtolower($type);
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
            $directory = $this->directory($type, $create_dir, true);
        }

        $filename  = $directory.\DIRECTORY_SEPARATOR.$filename;
        $filename  = FileSystem::normalizePath($filename);

        return $filename;
    }

    public function directory($type = '', $create_dir = true, $remote = false)
    {
        $output_filename = '';

        if (false !== $this->filename()) {
            $output_filename = $this->filename();
            $output_filename = str_replace($this->job_number.'_', '', $output_filename);
            $output_filename = $this->job_number.$output_filename;
        }


        $directory       = __FILES_DIR__.\DIRECTORY_SEPARATOR.__MEDIA_FILES_DIR__.\DIRECTORY_SEPARATOR.$output_filename;
        $type            = strtolower($type);
        switch ($type) {
            case 'xlsx':
                $directory .= \DIRECTORY_SEPARATOR.__XLSX_DIRECTORY__;

                break;
            case 'zip':
                $directory .= \DIRECTORY_SEPARATOR.__ZIP_DIRECTORY__;
                break;
            case 'upload':
                // if (Media::$Dropbox) {
                //     $directory = __TEMP_DIR__;
                // } elseif (Media::$Google) {
                //         $directory = __TEMP_DIR__;
                // } else {
                $directory = __FILES_DIR__.\DIRECTORY_SEPARATOR.'Uploads';
                // }
                break;
            case 'pdf':

                if($remote == true) {
                    $directory = __FILES_DIR__.\DIRECTORY_SEPARATOR.__MEDIA_FILES_DIR__.\DIRECTORY_SEPARATOR.$output_filename;
                } else {
                    $directory = __TEMP_DIR__.\DIRECTORY_SEPARATOR.__MEDIA_FILES_DIR__.\DIRECTORY_SEPARATOR.$output_filename;
                }
                //  dump($type, $directory, $remote);
                break;
        }
        $directory = FileSystem::platformSlashes($directory);
        $this->directory = FileSystem::normalizePath($directory);
        if (true == $create_dir) {
            $this->fileDriver->createFolder($directory);
        }

        return $this->directory;
    }
}
