<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Spreadsheet;

use CWP\Core\Media;
use CWP\Filesystem\Driver\MediaDropbox;
use Nette\Utils\FileSystem;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XLSXWriter extends Xlsx
{
    public $xls_path;

    public function write($filename)
    {
        if (Media::$Dropbox) {
            $d            = new MediaDropbox();
            $filename     = basename($filename);
            $tmp_file     = __TEMP_DIR__.\DIRECTORY_SEPARATOR.$filename;
            $dropbox_name = $this->xls_path.\DIRECTORY_SEPARATOR.$filename;
            parent::save($tmp_file);
            $file         = $d->save($tmp_file, $dropbox_name, ['autorename' => false]);
            FileSystem::delete($tmp_file);
        } else {
            parent::save($filename);
        }
    }
}
