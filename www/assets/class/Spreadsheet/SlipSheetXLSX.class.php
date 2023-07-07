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

    public $slipSheetArray;

    public function __construct($media, $quiet = false)
    {
        $this->media = $media;
        $this->exp = $media->exp;
        $this->conn = $media->conn;

        $result = $this->conn->fetchAll("SELECT * FROM form_data_count WHERE job_id = " . $media->job_id ." order by form_id ASC");
        foreach ($result as $id => $row) {
            $this->slipSheetArray[] = $row;
        }

        $sheets = array_chunk($this->slipSheetArray, 6);


        $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $this->createSlipSheet($this->spreadsheet, $sheets);


        $this->spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($this->spreadsheet);
        $new_xlsx_file = $media->getfilename('slips', null, true);
        $writer->save($new_xlsx_file);
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);


    }

    public function createslipsheet($sheetObj, $sheets)
    {
        $worksheet_title = "Quick Sheet";

        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($sheetObj, $worksheet_title);
        $sheetObj->addSheet($myWorkSheet, 0);
        $SlipSheet = $sheetObj->getSheet(0);


        $styles = new SlipSheetXLSX_Styles($SlipSheet);
        $styles->totalPages = count($sheets);

        $styles->sheetCommon();
        $styles->setColWidths();

        $row = 1;
        $lineIdx = 1;
        foreach($sheets as $pageNo => $page) {

            $col_A = "A";
            $col_B = "C";

            $row = 21 * $pageNo;
            $row++;

            foreach($page as $k => $v) {
                for($lineIdx = 1;$lineIdx <= 7; $lineIdx++) {
                    switch($lineIdx) {
                        case 1:
                            $styles->addSheetData($v->form_number, $col_A . $row);
                            break;
                        case 2:
                            if($v->packaging == "half") {
                                $text = "Half Skid";
                                $box = "Boxes";
                            }
                            if($v->packaging == "full") {
                                $text = "Full Skid";
                                $box = "Boxes";
                            }
                            if(str_contains($v->packaging, "cartons")) {
                                $text = $v->packaging;
                                $box = "Cartons";
                            }

                            $styles->addSheetData($text, $col_A .$row);
                            break;
                        case 3:
                            $styles->addSheetData($v->former ." ".$v->count." pcs", $col_A .$row);
                            break;
                        case 4:
                            $packageRow=$row;
                            if(str_contains($box, "Cartons")) {

                                $packageRow++;
                            }

                            if($v->full_boxes > 0) {
                                $styles->addSheetData($box, $col_A .$packageRow);
                                $styles->addSheetData($v->full_boxes, $col_B .$packageRow);
                            }
                            break;
                        case 5:
                            if($box !=  "Crtons") {
                                $styles->addSheetData("Layers", $col_A .$row);
                                $styles->addSheetData($v->layers_last_box, $col_B .$row);
                            }
                            break;
                        case 6:
                            $text_6 = "Lifts";
                            if($box ==  "Cartons") {
                                $text_6 = "Last Carton";
                            }
                            $styles->addSheetData($text_6, $col_A .$row);
                            $styles->addSheetData($v->lifts_last_layer, $col_B .$row);
                            break;

                    }
                    $row++;
                }

                if($k == 2) {

                    $row = 21 * $pageNo;
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





}
