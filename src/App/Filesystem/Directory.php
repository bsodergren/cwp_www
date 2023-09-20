<?php
/**
 * CWP Media tool for load flags
 */

use CWP\Filesystem\MediaFileSystem;
use Nette\Utils\FileSystem;

class Directory extends MediaFileSystem
{
    // private function __directory($type = '', $create_dir = true)
    // {
    //     $output_filename = '';

    //     if (false !== $this->__filename()) {
    //         $output_filename = \DIRECTORY_SEPARATOR.$this->__filename();
    //     }

    //     $directory = $output_filename;

    //     if ('xlsx' == strtolower($type)) {
    //         if (__USE_DROPBOX__ == false) {
    //             $directory .= __XLSX_DIRECTORY__;
    //         }
    //     }

    //     if ('slips' == strtolower($type)) {
    //         $directory .= __XLSX_SLIPS_DIRECTORY__;
    //     }

    //     if ('pdf' == strtolower($type)) {
    //         $directory = __PDF_UPLOAD_DIR__;
    //     }
    //     if ('zip' == strtolower($type)) {
    //         $directory .= __ZIP_FILE_DIR__;
    //     }

    //     if ('upload' == strtolower($type)) {
    //         $directory = __EMAIL_PDF_UPLOAD_DIR__;
    //     }

    //     if (defined('__MEDIA_FILES_DIR__')) {
    //         if (__MEDIA_FILES_DIR__ != '') {
    //             if (!is_dir(__MEDIA_FILES_DIR__)) {
    //                 FileSystem::createDir(__MEDIA_FILES_DIR__, 511);
    //             }
    //             $directory = __MEDIA_FILES_DIR__.$directory;
    //         } else {
    //             $directory = __FILES_DIR__.$directory;
    //         }
    //     } else {
    //         $directory = __FILES_DIR__.$directory;
    //     }

    //     $this->directory = FileSystem::normalizePath($directory);

    //     if (true == $create_dir) {
    //         FileSystem::createDir($this->directory, 511);
    //     }

    //     return $this->directory;
    // }

    // public function getDirectory($type = '', $create_dir = '')
    // {
    //     return $this->__directory($type, $create_dir);
    // }
}
