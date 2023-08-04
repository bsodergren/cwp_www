<?php
namespace CWP\Spreadsheet\MasterList;
/**
 * CWP Media tool
 */

use CWP\HTML\HTMLDisplay;
use CWP\Media\Media;
use CWP\Spreadsheet\Media\MediaXLSX_Styles;
use CWP\Utils;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SlipSheetXLSX;

class MasterXLSX extends Media
{
    protected object $spreadsheet;
    protected object $media;
    public $xlsx_array;
    public $form_details;
    public $box;
    public $trim_details = [];

    public function __construct(object $media, $quiet = false)
    {
        $this->media      = $media;

        $this->xlsx_array = $media->MediaArray;

        $keyidx           = array_key_first($this->xlsx_array);

        $this->job_number = $this->xlsx_array[$keyidx]['job_number'];
        $this->pdf_file   = $this->xlsx_array[$keyidx]['pdf_file'];
        $this->job_id     = $this->xlsx_array[$keyidx]['job_id'];
    }

    public function MasterWorkbook()
    {
        $result = Media::$connection->fetchAll('SELECT form_number FROM media_forms WHERE job_id = '.$this->media->job_id.'');

        foreach ($result as $data) {
            $form_number       = $data->form_number;
            $this->spreadsheet = new Spreadsheet();

            $this->spreadsheet->setActiveSheetIndex(0);
            $writer            = new Xlsx($this->spreadsheet);
            $new_xlsx_file     = $this->media->getfilename('slips', $form_number, true);
            $writer->save($new_xlsx_file);
            $this->spreadsheet->disconnectWorksheets();
            unset($this->spreadsheet);
        }
    }

    public function writeMasterWorkbook($sheetObj, $form_number, $f = 0)
    {
        foreach ($this->xlsx_array as $form_number => $data) {
            $sql    = 'SELECT * FROM form_data_count  WHERE job_id = '.$this->media->job_id.' AND form_number = '.$form_number.' order by former DESC ,form_id ASC';

            $result = Media::$connection->fetchAll($sql);
            foreach ($result as $form_data) {
                $MasterArray[$form_number][$form_data->former][$form_data->form_letter][] = [
                    'pub'              => $form_data->pub,
                    'count'            => $form_data->count,
                    'bind'             => $form_data->bind,
                    'packaging'        => $form_data->packaging,
                    'full_boxes'       => $form_data->full_boxes,
                    'layers_last_box'  => $form_data->layers_last_box,
                    'lifts_last_layer' => $form_data->lifts_last_layer,
                ];
            }
        }

        $worksheet_title = 'Master List';
        $masterWorkSheet = new Worksheet($sheetObj, $worksheet_title);
        $sheetObj->addSheet($masterWorkSheet, 0);
        $sheet           = $sheetObj->getSheet(0);
        $sheet->getHeaderFooter()->setOddHeader('&36&B '.__LANG_MEDIA_LOAD_FLAG);
        $sheet->getHeaderFooter()->setOddFooter('&L&B'.__LANG_MEDIA_LOAD_FLAG.'&RPage &P of &N');

        foreach ($MasterArray as $form_number => $data) {
            foreach ($data as $former => $formData) {
                foreach ($formData as $letter => $parts) {
                    //    dd($letter, $parts);
                }
            }
        }
    }

    public function writeWorkbooks()
    {
        foreach ($this->xlsx_array as $form_number => $data) {
            $this->spreadsheet = new Spreadsheet();
            $slipSheet         = new SlipSheetXLSX($this->media);
            $s_idx             = 1;

            $this->media->get_form_configuration($data);

            foreach ($data as $former => $result_array) {
                if ('Front' == $former || 'Back' == $former) {
                    foreach ($result_array as $form_letter => $form_details_array) {
                        foreach ($form_details_array as $key => $this->form_details) {
                            $this->calculateBox();
                            $this->addFormBoxData();

                            if ('full' == $this->box['packaging'] || 'half' == $this->box['packaging']) {
                                $tmp_box    = $this->box;
                                $full_boxes = $this->box['full_boxes'];
                                $max_boxes  = $this->box['full_boxes'];

                                if ($this->box['full_boxes'] >= 1) {
                                    if (0 != $this->box['layers_last_box'] && 0 != $this->box['lifts_last_layer']) {
                                        $this->box['layers_last_box']  = 0;
                                        $this->box['lifts_last_layer'] = 0;
                                        $this->box['full_boxes']       = '';
                                        $sk                            = 0;

                                        while ($full_boxes > 0) {
                                            ++$sk;
                                            $this->box['layers_last_box'] = $this->box['layers_per_skid'];
                                            $this->box['skid_count']      = "$sk of ".$max_boxes + 1;
                                            $this->form_details['count']  = ($this->box['layers_per_skid'] * $this->box['lifts_per_layer']) * $this->box['lift_size'];
                                            $this->createWorksheet($this->spreadsheet, $s_idx, $form_number, $form_letter);

                                            $full_boxes                   = $full_boxes - 1;
                                            $s_idx                        = $s_idx + 1;
                                        }
                                        // $s_idx=$s_idx+1;
                                        ++$sk;
                                        $this->box['skid_count']       = "$sk of ".$max_boxes + 1;
                                        $this->box['layers_last_box']  = $tmp_box['layers_last_box'];
                                        $this->box['lifts_last_layer'] = $tmp_box['lifts_last_layer'];
                                        $this->form_details['count']   = (($this->box['layers_last_box'] * $this->box['lifts_per_layer']) + $this->box['lifts_last_layer']) * $this->box['lift_size'];
                                    }
                                }
                            }

                            $this->createWorksheet($this->spreadsheet, $s_idx, $form_number, $form_letter);
                            $this->form_details = '';
                            $s_idx              = $s_idx + 1;
                        }
                    }
                }
            }

            $slipSheet->createSlipSheet($this->spreadsheet, $form_number, $s_idx);
            $this->writeMasterWorkbook($this->spreadsheet, $form_number, 0);

            $sheetIndex        = $this->spreadsheet->getIndex(
                $this->spreadsheet->getSheetByName('Worksheet')
            );
            $this->spreadsheet->removeSheetByIndex($sheetIndex);
            $this->spreadsheet->setActiveSheetIndex(0);
            $writer            = new Xlsx($this->spreadsheet);
            $new_xlsx_file     = $this->media->getfilename('xlsx', $form_number, true);
            $writer->save($new_xlsx_file);
            HTMLDisplay::put('Writing '.basename($new_xlsx_file), 'red');

            $this->spreadsheet->disconnectWorksheets();
            unset($this->spreadsheet);
        }

        Media::$explorer->table('media_job')->where('job_id', $this->media->job_id)->update(['xlsx_exists' => 1]);
    }

    public function createWorksheet($sheetObj, $sheet_index, $form_number, $form_letter)
    {
        $bindery_trim          = false;
        $packaging             = '';

        $layers_last_box       = '';
        $full_boxes            = '';
        $lifts_last_layer      = '';
        $lift_size             = '';
        $layers_per_skid       = '';
        $lifts_per_layer       = '';
        $max_carton            = '';
        $max_half_skid         = '';

        foreach ($this->box as $var => $value) {
            $$var = $value;
        }

        $pub_value             = $this->form_details['pub'];

        $this->getTrimData($pub_value, $this->form_details['bind']);
        $head_trim             = $this->trim_details['head_trim'];
        $foot_trim             = $this->trim_details['foot_trim'];
        $del_size              = $this->trim_details['size'];

        $ship_value            = $this->form_details['ship'];
        $delivery              =  strtolower($this->form_details['former']);

        if ('back' == $delivery || 1 == $this->form_details['face_trim']) {
            if (1 != $this->form_details['no_bindery']) {
                $this->form_details['market'] = '';
                $ship_value                   = __LANG_BINDERY;
                if (1 == $this->form_details['face_trim']) {
                    $ship_value = __LANG_BINDERY_FACETRIM;
                }
                $head_trim                    = 0;
                $foot_trim                    = 0;
                $bindery_trim                 = true;
            }
        }

        $worksheet_title       = $form_number.$form_letter.'_'.$delivery;

        $myWorkSheet           = new Worksheet($sheetObj, $worksheet_title);
        $sheetObj->addSheet($myWorkSheet, $sheet_index);
        $sheet                 = $sheetObj->getSheet($sheet_index);

        $sheet->getHeaderFooter()->setOddHeader('&36&B '.__LANG_MEDIA_LOAD_FLAG);
        $sheet->getHeaderFooter()->setOddFooter('&L&B'.__LANG_MEDIA_LOAD_FLAG.'&RPage &P of &N');

        $form['job_number']    =  $this->form_details['job_number'];
        $form['market']        =  $this->form_details['market'];
        $form['pub_value']     =  $pub_value;
        $form['ship_value']    =  $ship_value;
        $form['form_number']   =  $form_number;
        $form['form_letter']   =  $form_letter;
        $form['bindery_trim']  = $bindery_trim;
        $form['head_trim']     = $head_trim;
        $form['foot_trim']     = $foot_trim;
        $form['del_size']      = $del_size;

        $form['page_conf']     = $this->media->form_configuration['configuration'].' '.$this->media->form_configuration['paper_wieght'].'#';

        $styles                = new MediaXLSX_Styles($sheet);

        $count                 = str_replace(',', '', $this->form_details['count']);
        $sheet_labels          = [
            '13' => [$lift_size, __LANG_LIFT_SIZE],
            '21' => [$count, __LANG_TOTAL_COUNT],
        ];

        if ('small cartons' == $packaging || 'large cartons' == $packaging) { // $delivery == "front" )
            $form['packaging']  = $packaging;

            $labels['14']       = __LANG_LIFTS_PER_CARTON;
            $sheet_labels['15'] = [$layers_last_box, __LANG_PCS_PER_CARTON];

            $sheet_labels['17'] = [$full_boxes, __LANG_FULL_CARTON];
            $total_boxes        = $full_boxes;

            if ($lifts_last_layer > 0) {
                $sheet_labels['18'] = [$lifts_last_layer, __LANG_CNT_LAST_CARTON];
                $total_boxes        = $full_boxes + 1;
            } else {
                $sheet_labels['18'] = ['0', __LANG_CNT_LAST_CARTON];
            }

            $sheet_labels['19'] = [$total_boxes, __LANG_TOTAL_CARTONS];
        } else {
            $form['packaging']  = $packaging.' skid';
            // Skid packaging: half/full
            $labels['14']       = 'Lifts per Layer';
            $sheet_labels['15'] = [$layers_per_skid, __LANG_LAYERS_PER_SKID];

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

        $sheet_labels['14']    = [$lifts_per_layer, $labels['14']];

        $styles->createPage($form, $sheet_labels, __PAGES_PER_XLSX__);
    }

    public static function getFormTotals($data)
    {
        $total_count = 0;
        $pcs_count   = 0;

        foreach ($data as $key => $array) {
            $pcs_count   = Utils::toint($array['count']);

            $total_count = $total_count + $pcs_count;
        }

        return $total_count;
    }

    public function calculateBox()
    {
        $pcs_carton            = '';

        $layers_last_box       = '';
        $full_boxes            = '';
        $lifts_last_layer      = '';
        $lift_size             = '';
        $layers_per_skid       = '';
        $lifts_per_layer       = '';
        $max_carton            = '';
        $max_half_skid         = '';

        $face_trim             = $this->form_details['face_trim'];

        $pcs                   = str_replace(',', '', trim($this->form_details['count']));
        $config                = $this->media->form_configuration['configuration'];
        $paper_wieght          = $this->media->form_configuration['paper_wieght'];
        $carton_size           = $this->media->form_configuration['carton_size'];

        $delivery              =  strtolower($this->form_details['former']);

        $paper_size            = $this->media->form_configuration['paper_size'];
        $config                = str_replace('pg', '', $config);

        $res                   = Media::$explorer->table('paper_type')->select('id')->where('paper_wieght = ?  AND paper_size = ?  AND pages = ?', $paper_wieght, $paper_size, $config)->fetch();
        $res                   = Media::$explorer->table('paper_count')->where('paper_id', $res['id'])->fetch();

        foreach ($res as $var => $value) {
            $$var = $value;
        }

        if ($pcs <= $max_carton && 1 != $face_trim) {
            $package = 'carton';
        } elseif (($pcs > $max_carton || 1 == $face_trim) && $pcs <= $max_half_skid) {
            $package = 'half';
        } else {
            $package = 'full';
        }

        if ('back' == $delivery) {
            if ($pcs <= $max_half_skid) {
                $package = 'half';
            } else {
                $package = 'full';
            }
        }

        $lift_size             = $delivery.'_lift';

        if ('carton' == $package) {
            // lifts per carton
            $lifts_per_layer  = $pcs_carton / $$lift_size;

            $full_boxes       = floor($pcs / $pcs_carton);

            $lifts_last_layer =  $pcs - ($pcs_carton * $full_boxes);

            $package          = $carton_size.' '.$package.'s';

            $layers_last_box  = $pcs_carton;
        } else {
            $lifts_per_layer  = $package.'_skid_lifts_layer';

            $layers_per_skid  = $delivery.'_'.$package.'_skid_layers';
            // number of lifts in full count.

            $number_of_lifts  = ceil($pcs / $$lift_size);

            $lifts_in_box     = $$lifts_per_layer * $$layers_per_skid;

            $full_boxes       = floor($number_of_lifts / $lifts_in_box);

            $lifts_last_box   = $number_of_lifts - ($full_boxes * $lifts_in_box);

            $layers_last_box  = floor($lifts_last_box / $$lifts_per_layer);

            $lifts_last_layer = ceil($lifts_last_box - ($layers_last_box * $$lifts_per_layer));

            $lifts_per_layer  = $$lifts_per_layer;
        }

        $result                = [
            'packaging'        => $package,
            'full_boxes'       => $full_boxes,
            'layers_last_box'  => $layers_last_box,
            'lifts_last_layer' => $lifts_last_layer,
            'lift_size'        => $$lift_size,
            'lifts_per_layer'  => $lifts_per_layer,
        ];

        if (isset($$layers_per_skid)) {
            $result['layers_per_skid'] = $$layers_per_skid;
        }

        // return [ $package,$full_boxes,$layers_last_box,$lifts_last_layer,$$lift_size,$lifts_per_layer];
        $this->box             = $result;
    }

    public function addFormBoxData()
    {
        $form_box_data                = $this->box;
        $form_box_data['form_number'] = $this->form_details['form_number'];
        $form_box_data['form_letter'] = $this->form_details['form_letter'];
        $form_box_data['count']       = $this->form_details['count'];
        $form_box_data['job_id']      = $this->form_details['job_id'];
        $form_box_data['market']      = $this->form_details['market'];
        $form_box_data['pub']         = $this->form_details['pub'];
        $form_box_data['bind']        = $this->form_details['bind'];

        $form_box_data['former']      = $this->form_details['former'];
        $count                        = Media::$explorer->table('form_data_count')
            ->where('form_id', $this->form_details['form_id'])
            ->update($form_box_data);
        if (0 == $count) {
            $form_box_data['form_id'] = $this->form_details['form_id'];
            $count                    = 	Media::$explorer->table('form_data_count')->insert($form_box_data);
        }
    }

    public function getTrimData($publication, $bind)
    {
        $head   = null;
        $foot   = null;
        $get    = false;
        $insert = false;
        $pub    = $this->cleanPub($publication);
        $b      = strtolower($bind);

        if (!key_exists('pub', $this->trim_details)) {
            $get = true;
        } elseif ($this->trim_details['pub'] != $pub) {
            $get = true;
        }

        if (true === $get) {
            $res = Media::$explorer->table('pub_trim')->select('head_trim,foot_trim,delivered_size')->where('pub_name = ?  AND bind = ? ', $pub, $b)->fetch();
            if (null == $res) {
                $insert = true;
                $res    = Media::$explorer->table('pub_trim')->insert(['pub_name' => $pub, 'bind' => $b]);
            }

            if (is_object($res)) {
                $head               = $res->head_trim;
                $foot               = $res->foot_trim;
                $size               = $res->delivered_size;
                $this->trim_details = ['pub' => $publication, 'bind' => $bind, 'head_trim' => $head, 'foot_trim' => $foot, 'size' => $size];
            }

            if (true === $insert) {
                $this->getTrimData($publication, $bind);
            }
        }

        // $this->trim_details = ['pub'=>$publication,'bind'=>$bind,'head_trim'=>$head,'foot_trim'=>$foot,'size'=>$size];
        // return  $this->trim_details;
    }

    public function cleanPub($publication)
    {
        return self::CleanPublication($publication);
    }

    public static function CleanPublication($publication)
    {
        $pcs         = ['+', "'", '&'];
        $publication = str_replace($pcs, '', $publication);
        $publication = str_replace('É', 'E', $publication);
        $publication = str_replace('  ', ' ', $publication);
        $publication = str_replace(' ', '_', $publication);
        $publication = strtolower($publication);

        return $publication;
    }
}
