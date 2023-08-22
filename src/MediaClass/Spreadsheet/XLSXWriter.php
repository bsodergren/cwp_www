<?php
namespace CWP\Spreadsheet;
use CWP\Filesystem\DropBox;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class XLSXWriter extends Xlsx
{

    public function write($filename)
    {

        if (__USE_DROPBOX__ == true) {
            $d = new DropBox();
            $path = dirname(str_replace(dirname($filename, 3).'\\', '', $filename)).DIRECTORY_SEPARATOR;
            $d->createFolder($path);
            $filename = basename($filename);
            $tmp_file = __TEMP_DIR__.DIRECTORY_SEPARATOR.$filename;
            dd($tmp_file);
        }
        parent::save($filename);
    }

}