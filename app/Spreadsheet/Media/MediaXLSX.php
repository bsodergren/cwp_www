<?php
/**
 * CWP Media tool
 */

namespace CWP\Spreadsheet\Media;

use CWP\HTML\HTMLDisplay;
use CWP\Media\Media;
use CWP\Media\MediaPublication;
use CWP\Spreadsheet\Calculator;
use CWP\Spreadsheet\LarrySheets\LarrySheetsXLSX;
use CWP\Spreadsheet\Slipsheets\SlipSheetXLSX;
use CWP\Utils;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MediaXLSX extends Media
{
    protected object $spreadsheet;
    public object $media;
    public $xlsx_array;
    public $box;
    public $form_details;

    public $trim_details = [];

    public function __construct(object $media, $quiet = false)
    {
        $this->media = $media;
        $this->xlsx_array = $media->MediaArray;

        $keyidx = array_key_first($this->xlsx_array);
        $array = $this->xlsx_array[$keyidx]['details'];
        // $array          =  $this->xlsx_array[$keyidx];

        $this->job_number = $array['job_number'];
        $this->pdf_file = $array['pdf_file'];
        $this->job_id = $array['job_id'];
    }

    public function writeWorkbooks()
    {
        $calc = new Calculator($this->media);
        foreach ($this->xlsx_array as $form_number => $dataArray) {
            $data = $dataArray['forms'];
            // $data           = $dataArray;
            $this->spreadsheet = new Spreadsheet();
            $slipSheet = new SlipSheetXLSX($this->media);
           // $larrySheet = new LarrySheetsXLSX($this->media);
            $s_idx = 0;

            $this->media->get_form_configuration($dataArray['details']);

            foreach ($data as $former => $result_array) {
                if ('Front' == $former || 'Back' == $former) {
                    foreach ($result_array as $form_letter => $form_details_array) {
                        foreach ($form_details_array as $key => $this->form_details) {
                            $this->box = $calc->calculateBox($this->form_details);
                            $this->addFormBoxData();
                            if ('full' == $this->box['packaging'] || 'half' == $this->box['packaging']) {
                                $tmp_box = $this->box;
                                $full_boxes = $this->box['full_boxes'];
                                $max_boxes = $this->box['full_boxes'];

                                if ($this->box['full_boxes'] >= 1) {
                                    if (0 != $this->box['layers_last_box'] && 0 != $this->box['lifts_last_layer']) {
                                        $this->box['layers_last_box'] = 0;
                                        $this->box['lifts_last_layer'] = 0;
                                        $this->box['full_boxes'] = '';
                                        $sk = 0;

                                        while ($full_boxes > 0) {
                                            ++$sk;
                                            $this->box['layers_last_box'] = $this->box['layers_per_skid'];
                                            $this->box['skid_count'] = "$sk of ".$max_boxes + 1;
                                            $this->form_details['count'] = ($this->box['layers_per_skid'] * $this->box['lifts_per_layer']) * $this->box['lift_size'];
                                            $this->createWorksheet($this->spreadsheet, $s_idx, $form_number, $form_letter);

                                            $full_boxes = $full_boxes - 1;
                                            $s_idx = $s_idx + 1;
                                        }
                                        // $s_idx=$s_idx+1;
                                        ++$sk;
                                        $this->box['skid_count'] = "$sk of ".$max_boxes + 1;
                                        $this->box['layers_last_box'] = $tmp_box['layers_last_box'];
                                        $this->box['lifts_last_layer'] = $tmp_box['lifts_last_layer'];
                                        $this->form_details['count'] = (($this->box['layers_last_box'] * $this->box['lifts_per_layer']) + $this->box['lifts_last_layer']) * $this->box['lift_size'];
                                    }
                                }
                            }

                            $this->createWorksheet($this->spreadsheet, $s_idx, $form_number, $form_letter);
                            $this->form_details = '';
                            $s_idx = $s_idx + 1;
                        }
                    }
                }
            }

            $slipSheet->createSlipSheet($this->spreadsheet, $form_number, $s_idx);
          //  $larrySheet->createslipsheet($this->spreadsheet, $form_number, $s_idx);

            $sheetIndex = $this->spreadsheet->getIndex(
                $this->spreadsheet->getSheetByName('Worksheet')
            );
            $this->spreadsheet->removeSheetByIndex($sheetIndex);
            $this->spreadsheet->setActiveSheetIndex(0);
            $writer = new Xlsx($this->spreadsheet);
            $new_xlsx_file = $this->media->getfilename('xlsx', $form_number, true);
            $writer->save($new_xlsx_file);

            HTMLDisplay::pushhtml('stream/excel/file_msg', ['TEXT' => 'Writing '.basename($new_xlsx_file)]);

            $this->spreadsheet->disconnectWorksheets();
            unset($this->spreadsheet);
        }
        Media::$explorer->table('media_job')->where('job_id', $this->media->job_id)->update(['xlsx_exists' => 1]);
    }

    public function createWorksheet($sheetObj, $sheet_index, $form_number, $form_letter)
    {
        $bindery_trim = false;
        foreach ($this->box as $var => $value) {
            $$var = $value;
        }

        $pub_value = $this->form_details['pub'];
        $this->trim_details = MediaPublication::getTrimData($pub_value, $this->form_details['bind']);
        $head_trim = $this->trim_details['head_trim'];
        $foot_trim = $this->trim_details['foot_trim'];
        $del_size = $this->trim_details['size'];

        $ship_value = $this->form_details['ship'];
        $delivery = strtolower($this->form_details['former']);

        if ('back' == $delivery || 1 == $this->form_details['face_trim']) {
            if (1 != $this->form_details['no_bindery']) {
                // $this->form_details['market'] = "";
                $ship_value = __LANG_BINDERY;
                if (1 == $this->form_details['face_trim']) {
                    $ship_value = __LANG_BINDERY_FACETRIM;
                }
                $head_trim = 0;
                $foot_trim = 0;
                $bindery_trim = true;
            }
        }

        $worksheet_title = $form_number.$form_letter.'_'.$delivery;

        $myWorkSheet = new Worksheet($sheetObj, $worksheet_title);
        $sheetObj->addSheet($myWorkSheet, $sheet_index);
        $sheet = $sheetObj->getSheet($sheet_index);

        $sheet->getHeaderFooter()->setOddHeader('&36&B '.__LANG_MEDIA_LOAD_FLAG);
        $sheet->getHeaderFooter()->setOddFooter('&L&B'.__LANG_MEDIA_LOAD_FLAG.'&RPage &P of &N');

        $form['job_number'] = $this->form_details['job_number'];
        $form['market'] = $this->form_details['market'];
        $form['pub_value'] = $pub_value;
        $form['ship_value'] = $ship_value;
        $form['form_number'] = $form_number;
        $form['form_letter'] = $form_letter;
        $form['bindery_trim'] = $bindery_trim;
        $form['head_trim'] = $head_trim;
        $form['foot_trim'] = $foot_trim;
        $form['del_size'] = $del_size;

        $form['page_conf'] = $this->media->form_configuration['configuration'].' '.$this->media->form_configuration['paper_wieght'].'#';

        $styles = new MediaXLSX_Styles($sheet);

        $count = str_replace(',', '', $this->form_details['count']);
        $sheet_labels = [
            '13' => [$lift_size, __LANG_LIFT_SIZE],
            '21' => [$count, __LANG_TOTAL_COUNT],
        ];

        if ('small cartons' == $packaging || 'large cartons' == $packaging) { // $delivery == "front" )
            $form['packaging'] = $packaging;

            $labels['14'] = __LANG_LIFTS_PER_CARTON;
            $sheet_labels['15'] = [$layers_last_box, __LANG_PCS_PER_CARTON];

            $sheet_labels['17'] = [$full_boxes, __LANG_FULL_CARTON];
            $total_boxes = $full_boxes;

            if ($lifts_last_layer > 0) {
                $sheet_labels['18'] = [$lifts_last_layer, __LANG_CNT_LAST_CARTON];
                $total_boxes = $full_boxes + 1;
            } else {
                $sheet_labels['18'] = ['0', __LANG_CNT_LAST_CARTON];
            }

            $sheet_labels['19'] = [$total_boxes, __LANG_TOTAL_CARTONS];
        } else {
            $form['packaging'] = $packaging.' skid';
            // Skid packaging: half/full
            $labels['14'] = 'Lifts per Layer';
            $sheet_labels['15'] = [$layers_per_skid, __LANG_LAYERS_PER_SKID];

            $labels['16'] = 'Max Skid';
            $sheet_labels['16'] = [$max_skid, __LANG_MAX_SKID];

            // Lifts per Carton
            // Number of Layers
            if ($full_boxes > 0) {
                $sheet_labels['17'] = [$full_boxes, __LANG_NUMBER_OF_FULL_BOXES];
            }

            unset($form['skid_count']);

            if (isset($skid_count)) {
                $form['skid_count'] = $skid_count;
            }

            if ($layers_last_box > 0) {
                $sheet_labels['18'] = [$layers_last_box, __LANG_NUMBER_OF_FULL_LAYERS];
            }

            if ($lifts_last_layer > 0) {
                $sheet_labels['19'] = [$lifts_last_layer, __LANG_LIFTS_LAST_LAYER];
            }
        }

        $sheet_labels['14'] = [$lifts_per_layer, $labels['14']];

        $styles->createPage($form, $sheet_labels, __PAGES_PER_XLSX__);
    }

    public static function getFormTotals($data)
    {
        $total_count = 0;
        $pcs_count = 0;

        foreach ($data as $key => $array) {
            $pcs_count = Utils::toint($array['count']);

            $total_count = $total_count + $pcs_count;
        }

        return $total_count;
    }

    public function addFormBoxData()
    {
        $fields = [
            'form_number',
            'form_letter',
            'count',
            'job_id',
            'market',
            'pub',
            'bind',
            'former',
        ];

        $form_box_data = $this->box;

        foreach ($this->form_details as $key => $value) {
            if (in_array($key, $fields)) {
                $form_box_data[$key] = $value;
            }
        }

        $count = Media::$explorer->table('form_data_count')
            ->where('form_id', $this->form_details['form_id'])
            ->update($form_box_data);

        if (0 == $count) {
            $form_box_data['form_id'] = $this->form_details['form_id'];
            $count = Media::$explorer->table('form_data_count')->insert($form_box_data);
        }
    }
}
