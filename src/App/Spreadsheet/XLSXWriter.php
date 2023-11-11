<?php
/**
 * CWP Media tool for load flags
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
            $this->saveFile(new MediaDropBox,$filename);
        } elseif (Media::$Google) {
            $this->saveFile(new MediaGoogleDrive,$filename);
        } else {
            parent::save($filename);
        }
    }

    private function saveFile($object,$filename)
    {
        $filename     = basename($filename);
        $tmp_file     = __TEMP_DIR__.\DIRECTORY_SEPARATOR.$filename;
        $remote_name = $this->xls_path.\DIRECTORY_SEPARATOR.$filename;
        parent::save($tmp_file);
        $file         = $object->save($tmp_file, $remote_name);
        FileSystem::delete($tmp_file);
    }
}
