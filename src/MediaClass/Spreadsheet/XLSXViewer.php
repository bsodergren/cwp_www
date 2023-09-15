<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Spreadsheet;

use CWP\HTML\HTMLDisplay;
use CWP\Spreadsheet\Media\MediaXLSX;

class XLSXViewer
{
    public static function checkifexist($media)
    {
        global $_REQUEST,$_SERVER;

        $form_number = '';
        if (\array_key_exists('form_number', $_REQUEST)) {
            $form_number = $_REQUEST['form_number'];
        }

        $media->excelArray($form_number);
        $excel = new MediaXLSX($media, true);

        echo HTMLDisplay::JavaRefresh('/view.php?'.$_SERVER['QUERY_STRING'], 0);
    }
}
