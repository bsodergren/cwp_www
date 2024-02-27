<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Spreadsheet;

use CWP\Core\Media;
use CWP\Filesystem\Driver\MediaDropbox;
use CWP\Filesystem\Driver\MediaGoogleDrive;
use Nette\Utils\FileSystem;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XLSXWriter extends Xlsx
{
    public $xls_path;

    public function write($filename)
    {
        if (Media::$Dropbox) {
            $this->saveFile($filename, new MediaDropBox());
        } elseif (Media::$Google) {
            $this->saveFile($filename, new MediaGoogleDrive());
        } else {
            $this->saveFile($filename);
        }
    }

    private function saveFile($filename, $object = null)
    {
        $r_filename = '';
        if (null === $object) {
            parent::save($filename);
        } else {
            $filename = basename($filename);
            $tmp_file = __TEMP_DIR__.\DIRECTORY_SEPARATOR.$filename;
            $r_filename = $this->xls_path.\DIRECTORY_SEPARATOR.$filename;
            parent::save($tmp_file);
            $filename = $object->save($tmp_file, $r_filename);
            FileSystem::delete($tmp_file);
        }
    }
}
