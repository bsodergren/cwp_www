<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Spreadsheet;

use CWP\Filesystem\MediaDropbox;
use Nette\Utils\FileSystem;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XLSXWriter extends Xlsx
{
    public function write($filename)
    {
        if (__USE_DROPBOX__ == true) {
            $d = new MediaDropbox();
            $path = \dirname(str_replace(\dirname($filename, 3).'\\', '', $filename)).\DIRECTORY_SEPARATOR;
            $path = str_replace(basename(__FILES_DIR__), '', $path);
            $res = $d->createFolder($path);
            $filename = basename($filename);
            $tmp_file = __TEMP_DIR__.\DIRECTORY_SEPARATOR.$filename;
            $dropbox_name = $res.\DIRECTORY_SEPARATOR.$filename;
            parent::save($tmp_file);
            $file = $d->save($tmp_file, $dropbox_name, ['autorename' => false]);
            FileSystem::delete($tmp_file);
        } else {
            parent::save($filename);
        }
    }
}
