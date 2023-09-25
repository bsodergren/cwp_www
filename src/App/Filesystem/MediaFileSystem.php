<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem;

/*
 * CWP Media tool
 */

use CWP\Core\Media;
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

    public function getContents($path)
    {
        if (Media::$Dropbox) {
            $dropbox = new MediaDropbox();

            return $dropbox->getContents($path);
        } else {
            $f = new MediaFinder();
            $array = $f->search($path, '*.pdf');
            foreach ($array as $file) {
                $return[] = ['name' => basename($file), 'path' => $file];
            }

            return $return;
        }
    }

    public static function exists($file)
    {
        $fs = new self($file);

        if (Media::$Dropbox) {
            $dropbox = new MediaDropbox();

            return $dropbox->exists($file);
        } else {
            $directory = $fs->getDirectory('upload', true);
            $pdf_file = $directory.\DIRECTORY_SEPARATOR.$file;

            return file_exists($pdf_file);
        }
    }

    public static function uploadFile($filename, $dropboxFilename, $options = [])
    {
        if (Media::$Dropbox) {
            MediaDropbox::UploadFile($filename, $dropboxFilename, $options);
        }
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
        if ('pdf' == strtolower($type)) {
        }

        return $filename;
    }

    private function __directory($type = '', $create_dir = true)
    {
        $output_filename = '';

        if (false !== $this->__filename()) {
            $output_filename = $this->__filename();
            $output_filename = str_replace($this->job_number.'_', '', $output_filename);
            $output_filename = $this->job_number.$output_filename;
        }

        $directory = $output_filename;
        if (Media::$Dropbox) {
            if (__DROPBOX_FILES_DIR__ != '') {
                $directory = __DROPBOX_FILES_DIR__.\DIRECTORY_SEPARATOR.$output_filename;
            }
        }
        if ('xlsx' == strtolower($type)) {
            $directory .= \DIRECTORY_SEPARATOR.__XLSX_DIRECTORY__;
            if (Media::$Dropbox) {
                $create_dir = false;
            }
        }

        if ('zip' == strtolower($type)) {
            $directory .= \DIRECTORY_SEPARATOR.__ZIP_DIRECTORY__;
            $create_dir = true;
        }
        if ('upload' == strtolower($type)) {
            $directory = \DIRECTORY_SEPARATOR.'Uploads';
            $create_dir = true;
        }
        if (!Media::$Dropbox) {
            if (\defined('__MEDIA_FILES_DIR__')) {
                if (__MEDIA_FILES_DIR__ != '') {
                    if (!is_dir(__MEDIA_FILES_DIR__)) {
                        FileSystem::createDir(__MEDIA_FILES_DIR__, 511);
                    }
                    $directory = __MEDIA_FILES_DIR__.\DIRECTORY_SEPARATOR.$directory;
                }
            }
        }
        if ('pdf' == strtolower($type)) {
        }

        $directory = FileSystem::unixSlashes($directory);
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
        if (Media::$Dropbox) {
            $this->directory = $this->dropbox->getDirectory($type, $create_dir);
        } else {
            $this->directory = $this->getDirectory($type, $create_dir);
        }

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
        if (__USE_DROPBOX__ == true) {
            $d = new MediaDropbox();

            $msg = $d->deleteFile($file);

            return $msg;
        }

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
