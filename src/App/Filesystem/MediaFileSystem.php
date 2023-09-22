<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem;

/*
 * CWP Media tool
 */

use Nette\InvalidStateException;
use Nette\IOException;
use Nette\Utils\FileSystem;

class MediaFileSystem
{
    public $directory;

    public $dropbox;

    public $job_number;

    public $pdf_file;

    public function __construct($pdf_file = null, $job_number = null)
    {
        $this->job_number = $job_number;
        $this->pdf_file = $pdf_file;
        $this->dropbox = new MediaDropbox();
    }

    public function getFilename($type = '', $form_number = '', $create_dir = '')
    {
        return $this->__filename($type, $form_number, $create_dir);
    }

    private function __filename($type = '', $form_number = '', $create_dir = false)
    {
        $directory = '';

        if (!isset($this->pdf_file)) {
            return false;
        }

        $file = basename($this->pdf_file, '.pdf');
        $filename = $this->job_number.'_'.$file;

        if ('xlsx' == strtolower($type)) {
            $filename = $filename.'_FM'.$form_number.'.xlsx';
        }
        if ('slips' == strtolower($type)) {
            $filename = $filename.'_CountSlips_FM'.$form_number.'.xlsx';
        }
        if ('zip' == strtolower($type)) {
            if ('' != $form_number) {
                $filename = $filename.'_FM'.$form_number.'.zip';
            } else {
                $filename .= '.zip';
            }
        }
        if ('pdf' == strtolower($type)) {
            $filename = $this->pdf_file;
        }

        if ('' != $type) {
            $directory = $this->__directory($type, $create_dir);
        }

        $filename = $directory.\DIRECTORY_SEPARATOR.$filename;
        $filename = FileSystem::normalizePath($filename);

        return $filename;
    }

    private function __directory($type = '', $create_dir = true)
    {
        $output_filename = '';

        if (false !== $this->__filename()) {
            $output_filename = \DIRECTORY_SEPARATOR.$this->__filename();
        }

        $directory = $output_filename;

        if ('xlsx' == strtolower($type)) {
            if (__USE_DROPBOX__ == true) {
                $create_dir = false;
            } else {
                // $directory .= __XLSX_DIRECTORY__;
            }
        }

        if ('zip' == strtolower($type)) {
            // $directory .= __ZIP_FILE_DIR__;
        }

        if (\defined('__MEDIA_FILES_DIR__')) {
            if (__MEDIA_FILES_DIR__ != '' && __USE_DROPBOX__ == false) {
                if (!is_dir(__MEDIA_FILES_DIR__)) {
                    FileSystem::createDir(__MEDIA_FILES_DIR__, 511);
                }
                $directory = __MEDIA_FILES_DIR__.$directory;
            }
            // else {
            //    $directory = __FILES_DIR__.$directory;
            // }
        }

        if (__USE_DROPBOX__ == true && 'xlsx' == strtolower($type)) {
            // $directory = __DROPBOX_FILES_DIR__.$directory;
        } else {
            // $directory = __FILES_DIR__.$directory;
        }

        //  $directory = FileSystem::un($directory);

        $this->directory = FileSystem::normalizePath($directory);

        if (true == $create_dir) {
            FileSystem::createDir($this->directory, 511);
        }

        return $this->directory;
    }

    public function getDirectory($type = '', $create_dir = '')
    {
        return $this->__directory($type, $create_dir);
    }

    public function getDropboxDirectory($type = 'upload', $create_dir = false)
    {
        $this->directory = $this->dropbox->getDirectory($type, $create_dir);

        return $this->directory;
    }

    public static function rename($old, $new)
    {
        $msg = null;
        $old = FileSystem::platformSlashes($old);

        $new = FileSystem::platformSlashes($new);

        try {
            if (false == filesystem::rename($old, $new)) {
                throw new InvalidStateException();
            }
        } catch (InvalidStateException $e) {
            $msg = $e->getMessage();
        }

        return $msg;
    }

    public static function delete($file)
    {
        $msg = null;
        if (file_exists($file) || is_dir($file)) {
            try {
                FileSystem::delete($file);
            } catch (IOException $e) {
                $msg = $e->getMessage();
            }
        } else {
            $msg = $file.' not found';
        }

        return $msg;
    }
}
