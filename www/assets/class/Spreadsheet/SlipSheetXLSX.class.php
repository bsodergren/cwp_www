<?php


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

class SlipSheetXLSX extends Media
{
    protected object $spreadsheet;
    protected object $media;
    private object $SlipData;
    private object $styles;


    public $slipSheetArray;

    public function __construct($media, $quiet = false)
    {
        $this->media = $media;
        $this->exp = $media->exp;
        $this->conn = $media->conn;
    }


    public function CreateSlips()
    {
        $result = $this->conn->fetchAll("SELECT form_number FROM media_forms WHERE job_id = " . $this->media->job_id ."");


        foreach($result as $data) {
            $form_number = $data->form_number;
            $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            $this->createSlipSheet($this->spreadsheet, $form_number, 0);


            $this->spreadsheet->setActiveSheetIndex(0);
            $writer = new Xlsx($this->spreadsheet);
            $new_xlsx_file = $this->media->getfilename('slips', $form_number, true);
            $writer->save($new_xlsx_file);
            $this->spreadsheet->disconnectWorksheets();
            unset($this->spreadsheet);
        }
    }


    public function createslipsheet($sheetObj, $form_number, $sheetIndex)
    {

        $result = $this->conn->fetchAll("SELECT * FROM form_data_count WHERE job_id = " . $this->media->job_id ." AND form_number = ".$form_number ." order by form_id ASC");

        foreach ($result as $id => $row) {
            $slipSheetArray[] = $row;
        }

        $sheets = array_chunk($slipSheetArray, 6);

        $worksheet_title = "Quick Sheet";

        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($sheetObj, $worksheet_title);
        $sheetObj->addSheet($myWorkSheet, $sheetIndex);
        $SlipSheet = $sheetObj->getSheet($sheetIndex);


        $this->styles = new SlipSheetXLSX_Styles($SlipSheet);
        $this->styles->totalPages = count($sheets);

        $this->styles->sheetCommon();
        $this->styles->setColWidths($this->styles->Columns);
        $SlipSheetSize = count($this->styles->rowHeight);
        $rowOffset = $SlipSheetSize * 3;
        $row = 1;
        $lineIdx = 1;
        foreach($sheets as $pageNo => $page) {

            $col_A = "A";
            $col_B = "C";

            $row = $rowOffset * $pageNo;
            $row++;

            foreach($page as $k => $v) {
                $this->SlipData = $v;
                for($lineIdx = 1;$lineIdx <= $SlipSheetSize; $lineIdx++) {
                    switch($lineIdx) {
                        case 1:
                            break;
                        case 2:
                            $this->setForm($col_A, $row);
                            break;
                        case 3:
                            $box = $this->setPackaging($col_A, $row);
                            break;
                        case  4:
                            $this->setFormLocation($col_A, $row);
                            break;
                        case 5:
                            $this->setFormerInfo($col_A, $row);
                            break;
                        case 6:
                            $this->boxDataBoxes($col_A, $col_B, $row, $box);
                            break;
                        case 7:
                            $this->boxDataLayers($col_A, $col_B, $row, $box);
                            break;
                        case 8:
                            $this->boxDataLifts($col_A, $col_B, $row, $box);
                            break;
                    }
                    $row++;
                }

                if($k == 2) {

                    $row = $rowOffset * $pageNo;
                    $row++;
                    $col_A = "E";
                    $col_B = "G";
                }

                if($k == 0 && $col_A == "E") {

                    $col_A = "A";
                    $col_B = "C";
                }
            }
        }
    }


    private function setForm($column, $row)
    {
        $text = $this->SlipData->form_number.$this->SlipData->form_letter;
        $this->styles->addSheetData($text, $column . $row);
    }

    private function setPackaging($column, $row)
    {
        if($this->SlipData->packaging == "half") {
            $text = "Half Skid";
            $box = "Box";
        }
        if($this->SlipData->packaging == "full") {
            $text = "Full Skid";
            $box = "Box";
        }
        if(str_contains($this->SlipData->packaging, "cartons")) {
            $text = $this->SlipData->packaging;
            $box = "Cartons";
        }

        $this->styles->addSheetData(ucwords($text), $column .$row);
        return "Full ".$box;
    }

    private function setFormLocation($column, $row)
    {
        $text = ucwords(strtolower($this->SlipData->pub ." ".$this->SlipData->market)," /");
        $this->styles->addSheetData($text, $column .$row);
    }

    private function setFormerInfo($column, $row)
    {
        $text = $this->SlipData->former ." ".$this->SlipData->count." pcs";
        $this->styles->addSheetData($text, $column .$row);
    }

    private function boxDataBoxes($col_A, $col_B, $row, $box)
    {
        if(str_contains($box, "Cartons")) {
            $row++;
        }

        if($this->SlipData->full_boxes > 0) {
            $this->styles->addSheetData($box, $col_A .$row);
            $this->styles->addSheetData($this->SlipData->full_boxes, $col_B .$row);
        }
    }
    private function boxDataLayers($col_A, $col_B, $row, $box)
    {
        if(!str_contains($box, "Cartons")) {
            if($this->SlipData->layers_last_box != 0 )
            {
                $this->styles->addSheetData("Layers", $col_A .$row);
                $this->styles->addSheetData($this->SlipData->layers_last_box, $col_B .$row);
            }
        }
    }

    private function boxDataLifts($col_A, $col_B, $row, $box)
    {
        $text = "Lifts";
        if(str_contains($box, "Cartons")) {
            $text = "Last Carton";
        }

        if($this->SlipData->lifts_last_layer != 0 ) {
            $this->styles->addSheetData($text, $col_A .$row);
            $this->styles->addSheetData($this->SlipData->lifts_last_layer, $col_B .$row);
        }
    }

}