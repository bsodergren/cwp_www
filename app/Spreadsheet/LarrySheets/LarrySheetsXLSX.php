<?php
/**
 * CWP Media tool
 */

namespace CWP\Spreadsheet\LarrySheets;

/*
 * CWP Media tool
 */

use CWP\Media\Media;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LarrySheetsXLSX extends Media
{
    protected object $spreadsheet;
    protected object $media;
    private object $SlipData;
    private object $styles;

    public $slipSheetArray;

    public function __construct($media, $quiet = false)
    {
        $this->media = $media;
    }

    public function createslipsheet($sheetObj, $form_number, $sheetIndex)
    {
        $sql = 'SELECT * FROM form_data_count WHERE job_id = '.$this->media->job_id.' AND form_number = '.$form_number.' order by form_id ASC';

        $result                   = Media::$connection->fetchAll($sql);
        foreach ($result as $id => $row) {
            $slipSheetArray[] = $row;
        }

        $sheets                   = array_chunk($slipSheetArray, 6);

        $worksheet_title          = 'Larry Sheet';

        $myWorkSheet              = new Worksheet($sheetObj, $worksheet_title);
        $sheetObj->addSheet($myWorkSheet, $sheetIndex);
        $SlipSheet                = $sheetObj->getSheet($sheetIndex);

        $this->styles             = new LarrySheetsXLSX_Styles($SlipSheet);
        $this->styles->totalPages = count($sheets);

        $this->styles->sheetCommon();
        $this->styles->setColWidths($this->styles->Columns);
        $SlipSheetSize            = count($this->styles->rowHeight);
        $rowOffset                = $SlipSheetSize * 3;
        $row                      = 1;
        $lineIdx                  = 1;
        foreach ($sheets as $pageNo => $page) {
            $col_A = 'A';
            $col_B = 'C';

            $row   = $rowOffset * $pageNo;
            ++$row;

            foreach ($page as $k => $v) {
                $this->SlipData = $v;
                for ($lineIdx = 1; $lineIdx <= $SlipSheetSize; ++$lineIdx) {
                    switch ($lineIdx) {
                        case 1:
                            break;
                        case 2:
                            $this->setForm($col_A, $row);
                            break;
                        case 3:
                            $box = $this->setPackaging($col_A, $row);
                            break;
                        case 4:
                            $this->boxDataBoxes($col_A, $col_B, $row, $box);
                            break;
                        case 5:
                            $this->boxDataLayers($col_A, $col_B, $row, $box);
                            break;
                        case 6:
                            $this->boxDataLifts($col_A, $col_B, $row, $box);
                            break;
                        case 7:
                            break;
                        case 8:
                            $this->setFormLocation($col_A, $row);

                            //$this->setFormerInfo($col_B, $row);
                           $this->setPcsInfo($col_B, $row);
                    }
                    ++$row;
                }

                if (2 == $k) {
                    $row   = $rowOffset * $pageNo;
                    ++$row;
                    $col_A = 'E';
                    $col_B = 'G';
                }

                if (0 == $k && 'E' == $col_A) {
                    $col_A = 'A';
                    $col_B = 'C';
                }
            }
        }
    }

    private function setForm($column, $row)
    {
        $text = $this->SlipData->form_number.$this->SlipData->form_letter;
        $this->styles->addSheetData($text, $column.$row);
    }

    private function setPackaging($column, $row)
    {
        if ('half' == $this->SlipData->packaging) {
            $text = 'Half Skid';
            $box  = 'Box';
        }
        if ('full' == $this->SlipData->packaging) {
            $text = 'Full Skid';
            $box  = 'Box';
        }
        if (str_contains($this->SlipData->packaging, 'cartons')) {
            $text = $this->SlipData->packaging;
            $box  = 'Cartons';
        }

        $this->styles->addSheetData(ucwords($text), $column.$row);

        return 'Full '.$box;
    }

    private function setFormLocation($column, $row)
    {
        // $text = ucwords(strtolower($this->SlipData->pub.' '.$this->SlipData->market), ' /');
        $text = ucwords(strtolower($this->SlipData->pub), ' /');

        $this->styles->addSheetData($text, $column.$row);
    }

    private function setFormerInfo($column, $row)
    {
        $text           = $this->SlipData->former;
        $this->styles->addSheetData($text, $column.$row);
    }

    private function setPcsInfo($column, $row)
    {
        $text = $this->SlipData->former ." ".number_format($this->SlipData->count).' pcs';
        $this->styles->addSheetData($text, $column.$row);
    }

    private function boxDataBoxes($col_A, $col_B, $row, $box)
    {
        if (str_contains($box, 'Cartons')) {
            ++$row;
        }

        if ($this->SlipData->full_boxes > 0) {
            $this->styles->addSheetData($box, $col_A.$row);
            $this->styles->addSheetData($this->SlipData->full_boxes, $col_B.$row);
        }
    }

    private function boxDataLayers($col_A, $col_B, $row, $box)
    {
        if (!str_contains($box, 'Cartons')) {
            if (0 != $this->SlipData->layers_last_box) {
                $this->styles->addSheetData('Layers', $col_A.$row);
                $this->styles->addSheetData($this->SlipData->layers_last_box, $col_B.$row);
            }
        }
    }

    private function boxDataLifts($col_A, $col_B, $row, $box)
    {
        $text = 'Lifts';
        if (str_contains($box, 'Cartons')) {
            $text = 'Last Carton';
        }

        if (0 != $this->SlipData->lifts_last_layer) {
            $this->styles->addSheetData($text, $col_A.$row);
            $this->styles->addSheetData($this->SlipData->lifts_last_layer, $col_B.$row);
        }
    }
}
