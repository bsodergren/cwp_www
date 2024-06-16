<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Spreadsheet;

use CWP\Core\Media;
use  CWPDisplay\HTML\HTMLDisplay;
use CWP\Spreadsheet\Media\MediaXLSX;
use  CWPDisplay\Template\Pages\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class XLSXViewer
{
    public $media;
    public $excel_file;
    public $quicksheet_index;

    public $sheet_names;

    public $file_id;

    public $params;
    public $PageLinks;
    public $QuickSheet;
    // public $QuickSheet;
    // public $QuickSheet;
    public $custom_css;

    public $reader;
    public $spreadsheet;
    public $sheet_id;

    public function __construct($excel_file, $file_id)
    {
        global $_REQUEST;
        if (array_key_exists('sheet_id', $_REQUEST)) {
            $this->sheet_id = $_REQUEST['sheet_id'];
        }
        $this->file_id = $file_id;
        $this->excel_file = $excel_file;
        $this->reader = IOFactory::createReader('Xlsx');
        $this->spreadsheet = $this->reader->load($excel_file);
        $this->sheet_names = $this->spreadsheet->getSheetNames();
    }

    public function getExcelPage()
    {
        $writer = IOFactory::createWriter($this->spreadsheet, 'Html');

        $writer->setSheetIndex($this->sheet_id);
        $custom_css = $writer->generateStyles(true);

        $rep_array = [
            '{border: 1px solid black;}' => '{border: 0px dashed red;}',
            'font-size:11pt;' => '',
            'font-size:11pt' => '',
            ' height:5pt' => ' height:0pt',
            ' height:25pt' => ' height:20pt',
            ' height:30pt' => ' height:25pt',
            ' height:35pt' => ' height:30pt',
            'tr { height:15pt }' => 'tr { height:0pt }',
            // 'page-break-before: always;' => 'display: none; page-break-before: always; page-break-after: auto;',
            // 'page-break-after: always;' => 'page-break-after: auto;',
            // '@media screen {' => '@media screen {'."\n".'.header { display: none; }',
            // '@media print {' => '@media print {'."\n".'.header {  }
            // ',
        ];

        if ($this->quicksheet_index != $this->sheet_id) {
            $rep_array['page-break-before: always;'] = 'display: none; page-break-before: always; page-break-after: auto;';
            $rep_array['page-break-after: always;'] = 'page-break-after: auto;';
            $rep_array['@media screen {'] = '@media screen {'."\n".'.header { display: none; }';
            $rep_array['@media print {'] = '@media print {'."\n".'.header {  }
    ';
        }

        foreach ($rep_array as $find => $replace) {
            $custom_css = str_replace($find, $replace, $custom_css);
        }

        $this->custom_css = $custom_css;

        return $writer->generateSheetData();
    }

    public function buildPage()
    {
        foreach ($this->sheet_names as $sheet_index => $sheet_name) {
            if (true == $this->QuickSheet($sheet_index, $sheet_name)) {
                continue;
            }

            $class = 'enabled';
            if ($this->sheet_id == $sheet_index) {
                $class = 'disabled';
            }

            [$name,$_] = explode(' ', $sheet_name);
            [$sheetName,$former] = explode('_', $name);
            if (!isset($last)) {
                $last = '';
            }

            $cellValue = ucwords(strtolower(
                $this->spreadsheet->getSheet($sheet_index)->getCellByColumnAndRow(2, 8)->getValue()
            ));
            $sheet_form_array[$former][] = View::SheetLink(
                $sheetName.' '.$cellValue,
                __URL_PATH__.'/view.php?job_id='.$this->media->job_id.'&file_id='.$this->file_id.'&sheet_id='.$sheet_index,
                'btn-success',
                '--bs-bg-opacity: .5;',
                $class,
            );
        }

        foreach ($sheet_form_array as $former => $buttons) {
            $button[0] = View::formerButton($former);
            $buttons = array_merge($button, $buttons);
            $sheet_links_html = implode("\n", $buttons);
            $this->params['SHEET_LIST_HTML'] .= View::SheetList($sheet_links_html);
        }
    }

    public function QuickSheet($sheet_index, $sheet_name)
    {
        if ('Quick Sheet' == $sheet_name) {
            $this->quicksheet_index = $sheet_index;

            $this->params['QUICK_SHEET'] = View::SheetLink(
                'quicksheet',
                __URL_PATH__.'/view.php?job_id='.$this->media->job_id.'&file_id='.$this->file_id.'&sheet_id='.$sheet_index.'&quicksheet=1',
                'btn-info',
                '--bs-bg-opacity: .5;',
                'enabled'
            );

            return true;
        }

        return false;
    }

    public static function checkifexist($media)
    {
        global $_REQUEST,$_SERVER;

        $form_number = '';
        if (\array_key_exists('form_number', $_REQUEST)) {
            $form_number = $_REQUEST['form_number'];
        }

        $media->excelArray($form_number);
        $excel = new MediaXLSX($media, true);

        echo Elements::JavaRefresh('/view.php?'.$_SERVER['QUERY_STRING'], 0);
    }
}
