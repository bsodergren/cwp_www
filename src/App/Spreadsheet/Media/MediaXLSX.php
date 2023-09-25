<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Spreadsheet\Media;

use CWP\Core\Media;
use CWP\Filesystem\MediaDropbox;
use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaPublication;
use CWP\Spreadsheet\Calculator;
use CWP\Spreadsheet\LarrySheets\LarrySheetsXLSX;
use CWP\Spreadsheet\Slipsheets\SlipSheetXLSX;
use CWP\Spreadsheet\XLSXWriter;
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

    public $sheet_labels = [];
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
            $new_xlsx_file = $this->media->getfilename('xlsx', $form_number, true);
            if (__USE_DROPBOX__ == true) {
                $d = new MediaDropbox();
                $pos = strrpos($new_xlsx_file, '\\');
                $path = substr($new_xlsx_file, 0, $pos);
                $this->dropbox_path = $d->createFolder($path);
            }

            $data = $dataArray['forms'];
            // $data           = $dataArray;
            $this->spreadsheet = new Spreadsheet();
            $slipSheet = new SlipSheetXLSX($this->media);
            //   $larrySheet = new LarrySheetsXLSX($this->media);
            $s_idx = 0;

            $this->media->get_form_configuration($dataArray['details']);

            foreach ($data as $former => $result_array) {
                if ('Front' == $former || 'Back' == $former) {
                    foreach ($result_array as $form_letter => $form_details_array) {
                        foreach ($form_details_array as $key => $this->form_details) {
                            $this->form_details['job_number'] = $this->job_number;
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
                                            --$full_boxes;
                                            ++$s_idx;
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
                            ++$s_idx;
                        }
                    }
                }
            }

            $slipSheet->createSlipSheet($this->spreadsheet, $form_number, $s_idx);
            // $larrySheet->createslipsheet($this->spreadsheet, $form_number, $s_idx);

            $sheetIndex = $this->spreadsheet->getIndex(
                $this->spreadsheet->getSheetByName('Worksheet')
            );
            $this->spreadsheet->removeSheetByIndex($sheetIndex);
            $this->spreadsheet->setActiveSheetIndex(0);
            $writer = new XLSXWriter($this->spreadsheet);
            $writer->xls_path = $this->dropbox_path;
            $writer->write($new_xlsx_file);
            HTMLDisplay::pushhtml('stream/excel/file_msg', ['TEXT' => 'Writing '.basename($new_xlsx_file)]);
            $this->spreadsheet->disconnectWorksheets();
            unset($this->spreadsheet);
        }

        Media::$explorer->table('media_job')->where('job_id', $this->media->job_id)->update(['xlsx_exists' => 1]);
    }

    public function createWorksheet($sheetObj, $sheet_index, $form_number, $form_letter)
    {
        $bindery_trim = false;
        /*
                "packaging" => "small cartons"
                "full_boxes" => 2.0
                "layers_last_box" => 2500
                "lifts_last_layer" => 900.0
                "lift_size" => 1250
                "lifts_per_layer" => 2
                "max_skid" => 17500
                */

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

        $packageMethod = str_replace(' ', '_', $this->box['packaging']);
        $this->sheet_labels = [];
        $form = $this->$packageMethod($form);
        $this->getLabels();

        $styles->createPage($form, $this->sheet_labels, __PAGES_PER_XLSX__);
    }

    public function getLabels()
    {
        foreach ($this->box as $var => $value) {
            $$var = $value;
        }

        if (str_contains($packaging, 'carton')) {
            $labels['14'] = __LANG_LIFTS_PER_CARTON;
            $labels['15'] = __LANG_PCS_PER_CARTON;
            $label_value['15'] = $layers_last_box;
        } else {
            $labels['14'] = 'Lifts per Layer';
            $labels['15'] = __LANG_LAYERS_PER_SKID;
            $label_value['15'] = $layers_per_skid;
            $labels['16'] = 'Max Skid';
            $this->sheet_labels['16'] = [$max_skid, __LANG_MAX_SKID];
        }

        $count = str_replace(',', '', $this->form_details['count']);
        $this->sheet_labels['13'] = [$this->box['lift_size'], __LANG_LIFT_SIZE];
        $this->sheet_labels['14'] = [$lifts_per_layer, $labels['14']];
        $this->sheet_labels['15'] = [$label_value['15'], $labels['15']];

        $this->sheet_labels['17_C'] = [$count, __LANG_TOTAL_COUNT];
    }

    public function cartons($form)
    {
        foreach ($this->box as $var => $value) {
            $$var = $value;
        }

        $form['packaging'] = $packaging;

        $this->sheet_labels['13_C'] = [$full_boxes, __LANG_FULL_CARTON];
        $total_boxes = $full_boxes;

        if ($lifts_last_layer > 0) {
            $this->sheet_labels['14_C'] = [$lifts_last_layer, __LANG_CNT_LAST_CARTON];
            $total_boxes = $full_boxes + 1;
        } else {
            $this->sheet_labels['14_C'] = ['0', __LANG_CNT_LAST_CARTON];
        }

        $this->sheet_labels['15_C'] = [$total_boxes, __LANG_TOTAL_CARTONS];

        return $form;
    }

    public function skid($form)
    {
        foreach ($this->box as $var => $value) {
            $$var = $value;
        }

        $form['packaging'] = $packaging.' skid';

        if ($full_boxes > 0) {
            $this->sheet_labels['17_C'] = [$full_boxes, __LANG_NUMBER_OF_FULL_BOXES];
        }

        unset($form['skid_count']);

        if (isset($skid_count)) {
            $form['skid_count'] = $skid_count;
        }

        if ($layers_last_box > 0) {
            $this->sheet_labels['13_C'] = [$layers_last_box, __LANG_NUMBER_OF_FULL_LAYERS];
        }

        if ($lifts_last_layer > 0) {
            $this->sheet_labels['14_C'] = [$lifts_last_layer, __LANG_LIFTS_LAST_LAYER];
        }

        return $form;
    }

    public function small_cartons($form)
    {
        return $this->cartons($form);
    }

    public function large_cartons($form)
    {
        return $this->cartons($form);
    }

    public function half($form)
    {
        return $this->skid($form);
    }

    public function full($form)
    {
        return $this->skid($form);
    }

    public static function getFormTotals($data)
    {
        $total_count = 0;
        $pcs_count = 0;

        foreach ($data as $key => $array) {
            $pcs_count = Utils::toint($array['count']);

            $total_count += $pcs_count;
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
            if (\in_array($key, $fields)) {
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
