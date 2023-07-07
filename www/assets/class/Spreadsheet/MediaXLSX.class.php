<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\Wizard;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

class MediaXLSX extends Media
{
    protected object $spreadsheet;
    protected object $media;

    public function __construct($media, $quiet = false)
    {

        $this->media = $media;
        $this->exp = $media->exp;

        $this->xlsx_array = $media->MediaArray;

        $keyidx = array_key_first($this->xlsx_array);

        $this->job_number = $this->xlsx_array[$keyidx]["job_number"];
        $this->pdf_file = $this->xlsx_array[$keyidx]["pdf_file"];
        $this->job_id = $this->xlsx_array[$keyidx]["job_id"];

        foreach ($this->xlsx_array as $form_number => $data) {
            $this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $s_idx = 0;

            $this->media->get_form_configuration($data);

            foreach ($data as $former => $result_array) {
                if ($former == "Front" || $former == "Back") {
                    foreach ($result_array as $form_letter => $form_details_array) {
                        foreach ($form_details_array as $key => $this->form_details) {

                            $this->calculateBox();

                            if ($this->box['packaging'] == "full" || $this->box['packaging'] == "half") {
                                $tmp_box = $this->box;
                                $full_boxes = $this->box['full_boxes'];
                                $max_boxes = $this->box['full_boxes'];
                                if ($this->box['full_boxes'] >= 1) {


                                    $this->box["layers_last_box"] = 0;
                                    $this->box["lifts_last_layer"] = 0;
                                    $this->box['full_boxes'] = '';
                                    $sk = 0;
                                    while ($full_boxes > 0) {
                                        $sk++;
                                        $this->box['layers_last_box'] = $this->box['layers_per_skid'];
                                        $this->box['skid_count'] = "$sk of " . $max_boxes + 1;
                                        $this->form_details['count'] = ($this->box['layers_per_skid'] * $this->box['lifts_per_layer']) * $this->box["lift_size"];
                                        $this->createWorksheet($this->spreadsheet, $s_idx, $form_number, $form_letter);

                                        $full_boxes = $full_boxes - 1;
                                        $s_idx = $s_idx + 1;
                                    }
                                    //$s_idx=$s_idx+1;
                                    $sk++;
                                    $this->box['skid_count'] = "$sk of " . $max_boxes + 1;
                                    $this->box["layers_last_box"] = $tmp_box["layers_last_box"];
                                    $this->box["lifts_last_layer"] = $tmp_box["lifts_last_layer"];
                                    $this->form_details['count'] = (($this->box['layers_last_box'] * $this->box['lifts_per_layer']) + $this->box["lifts_last_layer"]) * $this->box["lift_size"];
                                }
                            }


                            $this->createWorksheet($this->spreadsheet, $s_idx, $form_number, $form_letter);
                            $this->form_details = '';
                            $s_idx = $s_idx + 1;
                        }
                    }
                }
            }

            $this->spreadsheet->setActiveSheetIndex(0);
            $writer = new Xlsx($this->spreadsheet);
            $new_xlsx_file = $media->getfilename('xlsx', $form_number, true);
            $writer->save($new_xlsx_file);
            if($quiet === false) {
                HTMLDisplay::output("Writing " . basename($new_xlsx_file), "<br>");
                ob_flush();
            }
            $this->spreadsheet->disconnectWorksheets();
            unset($this->spreadsheet);
        }

        $this->exp->table('media_job')->where('job_id', $media->job_id)->update(['xlsx_exists' => 1]);
    }

    public function createWorksheet($sheetObj, $sheet_index, $form_number, $form_letter)
    {
        $bindery_trim = false;

        foreach ($this->box as $var => $value) {
            $$var = $value;
        }

        $pub_value = $this->form_details["pub"];
        $ship_value = $this->form_details['ship'];
        $delivery =  strtolower($this->form_details['former']);

        if ($delivery  == "back" || $this->form_details['face_trim'] == 1) {
            if ($this->form_details['no_bindery'] != 1) {
                $this->form_details['market'] = __LANG_BINDERY;
                $ship_value = __LANG_BINDERY;
                $bindery_trim = true;
            }
        }

        $worksheet_title = $form_number . $form_letter . "_" . $delivery;

        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($sheetObj, $worksheet_title);
        $sheetObj->addSheet($myWorkSheet, $sheet_index);
        $sheet = $sheetObj->getSheet($sheet_index);

        $sheet->getHeaderFooter()->setOddHeader('&36&B ' . __LANG_MEDIA_LOAD_FLAG);

        $styles = new MediaXLSX_Styles($sheet);
        $styles->setColWidths();
        $styles->setRowHeights();
        $styles->sheetCommon();


        $sheet->setCellValue('B6', $this->form_details['job_number']);
        $sheet->setCellValue('B7', $this->form_details['market']);
        $sheet->setCellValue('B8', $pub_value);
        $sheet->setCellValue('B9', $ship_value);
        $sheet->getStyle('B7')->getAlignment()->setShrinkToFit(true);

        $sheet->setCellValue('D6', $form_number . "" . $form_letter);

        $styles->cellBorder("A6:C10", "allBorders");
        $styles->cellBorder("D6", "outline");

        $sheet->setCellValue('D7', $this->media->form_configuration['configuration'] . " " . $this->media->form_configuration['paper_wieght'] . "#");

        $count = str_replace(",", "", $this->form_details['count']);

        $labels = array();

        $sheet_labels = array(
            '13' => array($lift_size, __LANG_LIFT_SIZE),
            '21' => array($count, __LANG_TOTAL_COUNT )
        );


        $sheet->getStyle('B21')->getNumberFormat()->setFormatCode('#,##0');

        if (($packaging == "small cartons" || $packaging == "large cartons")) { // $delivery == "front" )
            $sheet->setCellValue('B10', $packaging);

            $labels['14'] = __LANG_LIFTS_PER_CARTON;
            $sheet_labels['15'] = array($layers_last_box, __LANG_PCS_PER_CARTON);

            $sheet_labels['17'] = array($full_boxes, __LANG_FULL_CARTON);
            $total_boxes = $full_boxes;

            if ($lifts_last_layer > 0) {

                $sheet_labels['18'] = array($lifts_last_layer, __LANG_CNT_LAST_CARTON);
                $total_boxes = $full_boxes + 1;
            } else {
                $sheet_labels['18'] = array("0", __LANG_CNT_LAST_CARTON);
            }

            $sheet_labels['19'] = array($total_boxes, __LANG_TOTAL_CARTONS);
        } else {
            //Skid packaging: half/full
            $sheet->setCellValue('B10', $packaging . " skid");
            $labels['14'] = "Lifts per Layer";
            $sheet_labels['15'] = array($layers_per_skid, __LANG_LAYERS_PER_SKID);


            // Lifts per Carton
            // Number of Layers
            if ($full_boxes > 0) {
                $sheet_labels['17'] = array($full_boxes, __LANG_NUMBER_OF_FULL_BOXES);
            }

            if (isset($skid_count)) {
                $sheet->setCellValue('D8', $skid_count);
            }

            $sheet_labels['18'] = array($layers_last_box,__LANG_NUMBER_OF_FULL_LAYERS );
            if ($lifts_last_layer > 0) {
                $sheet_labels['19'] = array($lifts_last_layer, __LANG_LIFTS_LAST_LAYER);
            }
        }


        $sheet_labels['14'] = array($lifts_per_layer, $labels['14']);

        foreach ($sheet_labels as $key => $val) {
            $styles->addSheetData($val[0], $val[1], "A" . $key, "B" . $key);
        }



        $styles->cellBorder("A10", "bottom");
        $styles->cellBorder("A10", "right");
        $styles->cellBorder("B10", "bottom");
        /*
    if ($full_boxes == 0 )
    {
        $sheet->setCellValue('B16', $layers_last_box);
        // lifts last layer
        $sheet->setCellValue('B17', $lifts_last_layer);
    } else {
        $sheet->setCellValue('B15', $layers_last_box);
        $sheet->setCellValue('B16', $full_boxes);

    }

    if ($lifts_last_layer > 0 ) {
        $sheet->setCellValue('B17', $lifts_last_layer);
    }
    */
        if ($bindery_trim == true) {
            $sheet->setCellValue('A24', __LANG_BINDERY);
            $sheet->setCellValue('A25', __LANG_BINDERY);
            $sheet->setCellValue('A26', __LANG_BINDERY);
        }
    }




    public static function getFormTotals($data)
    {
        $total_count = 0;
        $pcs_count  = 0;

        foreach ($data as $key => $array) {
            $pcs_count = Utils::toint($array['count']);

            $total_count = $total_count + $pcs_count;
        }

        return $total_count;
    }



    public function calculateBox()
    {



        $face_trim = $this->form_details["face_trim"];

        $pcs = str_replace(',', '', trim($this->form_details['count']));
        $config = $this->media->form_configuration['configuration'];
        $paper_wieght = $this->media->form_configuration['paper_wieght'];
        $carton_size = $this->media->form_configuration['carton_size'];

        $delivery =  strtolower($this->form_details['former']);

        $paper_size = $this->media->form_configuration['paper_size'];
        $config = str_replace("pg", "", $config);

        $res = $this->exp->table("paper_type")->select("id")->where("paper_wieght = ?  AND paper_size = ?  AND pages = ?", $paper_wieght, $paper_size, $config)->fetch();
        $res = $this->exp->table("paper_count")->where('paper_id', $res['id'])->fetch();

        foreach ($res as $var => $value) {
            $$var = $value;
        }


        if ($pcs <= $max_carton && $face_trim != 1) {
            $package = "carton";
        } elseif (($pcs > $max_carton || $face_trim == 1)  && $pcs <= $max_half_skid) {
            $package = "half";
        } else {
            $package = "full";
        }

        if ($delivery == "back") {
            if ($pcs <= $max_half_skid) {
                $package = "half";
            } else {
                $package = "full";
            }
        }

        $lift_size = $delivery . "_lift";

        if ($package == "carton") {

            // lifts per carton
            $lifts_per_layer = $pcs_carton / $$lift_size;

            $full_boxes = floor($pcs / $pcs_carton);

            $lifts_last_layer =  $pcs - ($pcs_carton * $full_boxes);

            $package = $carton_size . " " . $package . "s";

            $layers_last_box = $pcs_carton;
        } else {
            $lifts_per_layer = $package . "_skid_lifts_layer";

            $layers_per_skid = $delivery . "_" . $package . "_skid_layers";
            // number of lifts in full count.

            $number_of_lifts = ceil($pcs / $$lift_size);

            $lifts_in_box = $$lifts_per_layer * $$layers_per_skid;

            $full_boxes = floor($number_of_lifts / $lifts_in_box);

            $lifts_last_box = $number_of_lifts - ($full_boxes * $lifts_in_box);

            $layers_last_box = floor($lifts_last_box / $$lifts_per_layer);

            $lifts_last_layer = ceil($lifts_last_box - ($layers_last_box * $$lifts_per_layer));

            $lifts_per_layer = $$lifts_per_layer;
        }

        $result = array(
            "packaging" => $package,
            "full_boxes" => $full_boxes,
            "layers_last_box" => $layers_last_box,
            "lifts_last_layer" => $lifts_last_layer,
            "lift_size" => $$lift_size,
            "lifts_per_layer" => $lifts_per_layer
        );

        if (isset($$layers_per_skid)) {
            $result["layers_per_skid"] = $$layers_per_skid;
        }

        #return [ $package,$full_boxes,$layers_last_box,$lifts_last_layer,$$lift_size,$lifts_per_layer];
        $this->box = $result;
        $this->addFormBoxData();

    }

    public function addFormBoxData()
    {

        $form_box_data = $this->box;
        $form_box_data['form_number'] = $this->form_details['form_number'];
        $form_box_data['count'] = $this->form_details['count'];
        $form_box_data['job_id'] = $this->form_details['job_id'];
        $form_box_data['market'] = $this->form_details['market'];
        $form_box_data['former'] = $this->form_details['former'];
        $count = $this->exp->table('form_data_count')
            ->where('form_id', $this->form_details['form_id'])
            ->update($form_box_data);
        if($count == 0) {
            $form_box_data['form_id'] = $this->form_details['form_id'];
            $count = 	$this->exp->table("form_data_count")->insert($form_box_data);
        }
    }



}
