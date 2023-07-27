<?php
/**
 * CWP Media tool
 */

namespace CWP\Media;

use CWP\Utils;
use CWP\Media\MediaFileSystem;

/**
 * CWP Media tool.
 */

/**
 * @property mixed $job_id
 * @property mixed $pdf_file
 * @property mixed $xlsx
 * @property mixed $zip
 * @property mixed $location
 * @property mixed $job_number
 */
class Media
{
    public $exp;

    public $conn;

    private $mediaLoc;

    public $MediaArray = [];

    public $job_id;

    public $pdf_file;

    public $job_number;

    public $xlsx;

    public $zip;
    public $base_dir;
    public $pdf_tmp_file;
    public $pdf_fullname;
    public $xlsx_exists;

    public $xlsx_directory;
    public  $zip_directory;
    public $zip_file;
    public $form_config;


    public $location;

    public function __construct($MediaDB = '')
    {
        global $connection;
        global $explorer;
        $this->conn = $connection;
        $this->exp  = $explorer;

        if (is_object($MediaDB)) {
            $array   = get_object_vars($MediaDB);
            unset($MediaDB);
            $MediaDB = $array;
        }

        if (is_array($MediaDB)) {
            $this->job_id     = (empty($MediaDB['job_id'])) ? '' : $MediaDB['job_id'];
            $this->pdf_file   = (empty($MediaDB['pdf_file'])) ? '' : $MediaDB['pdf_file'];
            $this->job_number = (empty($MediaDB['job_number'])) ? '' : $MediaDB['job_number'];
            $this->xlsx       = (empty($MediaDB['xlsx_exists'])) ? '' : $MediaDB['xlsx_exists'];
            $this->zip        = (empty($MediaDB['zip_exists'])) ? '' : $MediaDB['zip_exists'];
            $this->location   = (empty($MediaDB['base_dir'])) ? '' : $MediaDB['base_dir'];

            $this->getDirectories();
        }

        // $this->getPubList([1,2]);
    }

    public function getPubList($ids)
    {
        // if(is_array($ids)) {
        $ids  = implode(',', $ids);
        // }

        $sql  = 'SELECT * FROM pub_trim WHERE id IN ('.$ids.');';
        $pubs = $this->conn->query($sql);
        foreach ($pubs as $row) {
            echo $row->id;
            echo $row->pub_name;
        }

        dd($pubs);
    }

    public function excelArray($form_number = null)
    {
        // global $explorer;

        $form_config = $this->get_drop_details($form_number);

        foreach ($form_config as $form_number => $vars) {
            $total_back_peices = 0;

            $prev_form_letter  = '';

            $sort              = ['SORT_FORMER' => 1, 'SORT_LETTER' => 1, 'SORT_PUB' => 1];

            $result            = $this->get_drop_form_data($form_number, $sort);

            foreach ($result as $row_id => $form_row) {
                $current_form_letter = $form_row['form_letter'];

                if ($prev_form_letter != $current_form_letter) {
                    $total_back_peices = 0;
                    $prev_form_letter  = $current_form_letter;
                }

                if (!isset($this->MediaArray[$form_number]['bind'])) {
                    $this->MediaArray[$form_number] = [
                        'bind'       => $vars['bind'],
                        'product'       => $vars['product'],
                        'count'       => $vars['count'],
                        'config'     => $vars['config'],
                        'job_number' => $form_row['job_number'],
                        'pdf_file'   => $form_row['pdf_file'],
                        'job_id'     => $this->job_id,
                    ];
                }

                if ('Back' == $form_row['former']) {
                    $total_back_peices                                                   = $total_back_peices + $form_row['count'];
                    $this->MediaArray[$form_number]['Back'][$form_row['form_letter']][0] = [
                        'form_id'     => $form_row['id'],
                        'form_number' => $form_row['form_number'],
                        'form_letter' => $form_row['form_letter'],
                        'job_id'      => $this->job_id,
                        'market'      => $form_row['market'],
                        'pub'         => $form_row['pub'],
                        'count'       => $total_back_peices,
                        'ship'        => $form_row['ship'],
                        'job_number'  => $form_row['job_number'],
                        'former'      => 'Back',
                        'face_trim'   => $form_row['face_trim'],
                        'no_bindery'  => $form_row['no_bindery'],
                        'bind'        => $vars['bind'],
                    ];
                } else {
                    $this->MediaArray[$form_number]['Front'][$form_row['form_letter']][] = [
                        'form_id'     => $form_row['id'],
                        'form_number' => $form_row['form_number'],
                        'form_letter' => $form_row['form_letter'],
                        'job_id'      => $this->job_id,
                        'market'      => $form_row['market'],
                        'pub'         => $form_row['pub'],
                        'count'       => $form_row['count'],
                        'ship'        => $form_row['ship'],
                        'job_number'  => $form_row['job_number'],
                        'former'      => 'Front',
                        'face_trim'   => $form_row['face_trim'],
                        'no_bindery'  => $form_row['no_bindery'],
                        'bind'        => $vars['bind'],
                    ];
                }
            }
            krsort($this->MediaArray[$form_number]);
        }

        $this->MediaArray;
    }

    private function getDirectories()
    {
        $this->mediaLoc       = new MediaFileSystem($this->pdf_file, $this->job_number);

        $this->base_dir       = $this->mediaLoc->getDirectory();

        $this->pdf_fullname   = $this->mediaLoc->getFilename('pdf');
        $this->pdf_tmp_file   = $this->pdf_fullname.'.~qpdf-orig';

        $this->xlsx_directory = $this->mediaLoc->getDirectory('xlsx');
        $this->zip_directory  = $this->mediaLoc->getDirectory('zip');
        $this->zip_file       = $this->mediaLoc->getFilename('zip');
    }

    public function getFilename($type = '', $form_number = '', $create_dir = '')
    {
        return $this->mediaLoc->getFilename($type, $form_number, $create_dir);
    }

    public function getDirectory($type = '', $create_dir = '')
    {
        return $this->mediaLoc->getDirectory($type, $create_dir);
    }

    public static function set_exists($value, $field, $job_id)
    {
        global $explorer;
        if (0 == $value) {
            $value = '';
        }
        $result = $explorer->table('media_job')->where('job_id', $job_id)->update([$field.'_exists' => $value]);
    }

    public static function get_exists($field, $job_id)
    {
        global $explorer;
        $result   = $explorer->table('media_job')->select($field.'_exists')->where('job_id', $job_id);
        $exists   = $result->fetch();
        $var_name = $field.'_exists';
        if (isset($exists->$var_name)) {
            return Utils::toint($exists->$var_name);
        }

        return null;
    }

    public function number_of_forms()
    {
        return $this->exp->table('media_forms')->where('job_id', $this->job_id)->count('*');
    }

    public function get_form_list()
    {
        $sql    = 'SELECT form_number FROM media_forms WHERE `job_id` = '.$this->job_id;
        $result = $this->conn->fetchAll($sql);

        return $result;
    }

    public function get_max_drop_forms()
    {
        $sql    = 'SELECT DISTINCT(`form_number`) as max FROM `media_forms` WHERE `job_id` = '.$this->job_id.'  ORDER BY `max` DESC limit 1';
        $result = $this->conn->fetch($sql);

        return $result['max'];
    }

    public function get_first_form()
    {
        $sql    = 'SELECT `form_number` as max FROM `media_forms` WHERE `job_id` = '.$this->job_id.' ORDER BY `max` ASC limit 1';
        $result = $this->conn->fetch($sql);

        return $result['max'];
    }

    public function get_drop_details($form_number = '')
    {
        $form              = '';

        if (true == $form_number) {
            $form = ' and `form_number`= '.$form_number;
        }

        $sql               = 'SELECT * FROM `media_forms` WHERE `job_id` = '.$this->job_id.$form;

        $result            = $this->conn->query($sql);

        $form_config       = [];

        foreach ($result as $idx => $data) {
            $form_config[$data['form_number']] = ['bind' => $data['bind'], 'config' => $data['config'],
            'product' => $data['product'],
            'count' => $data['count']];
        }

        $this->form_config = $form_config;

        return $form_config;
    }

    public function get_drop_form_data($form_number = '', $sort = [])
    {
        $add    = '';

        if (true == $form_number) {
            $FORM_SEQ = ' and `f`.`form_number` = '.$form_number;
        }

        if (array_key_exists('SORT_LETTER', $sort)) {
            if (isset($sort_query)) {
                $add = $sort_query.', ';
            }
            $sort_query = ' `f`.`form_letter` ASC ';
        }

        if (array_key_exists('SORT_NUMBER', $sort)) {
            if (isset($sort_query)) {
                $add = $sort_query.', ';
            }
            $sort_query = $add.' `f`.`form_number` ASC ';
        }

        if (array_key_exists('SORT_PUB', $sort)) {
            if (isset($sort_query)) {
                $add = $sort_query.', ';
            }
            $sort_query = $add.' `f`.`pub` ASC ';
        }

        if (array_key_exists('SORT_FORMER', $sort)) {
            if (isset($sort_query)) {
                $add = $sort_query.', ';
            }
            $sort_query = $add.' `f`.`former` ASC ';
        }

        if (isset($sort_query)) {
            $sort_query = ' ORDER BY '.$sort_query;
        } else {
            $sort_query = '';
        }

        $sql    = 'SELECT `f`.`id`,`f`.`job_id`,`f`.`form_number`,`f`.`form_letter`,`f`.`market`,`f`.`pub`,`f`.`count`,`f`.`ship`,`f`.`former`,`f`.`face_trim`,`f`.`no_bindery`,`m`.`job_number`, `m`.`pdf_file` FROM `form_data` f, `media_job` m WHERE ( `f`.`job_id` = '.$this->job_id.' and `m`.`job_id` = '.$this->job_id.$FORM_SEQ.' ) '.$sort_query;

        $result = $this->conn->fetchAll($sql);

        return $result;
    }

    public function get_form_configuration($data)
    {
        $config                              = $data['config'];
        list($bind_type, $jog, $carton_code) = str_split($data['bind']);

        switch ($bind_type) {
            case 'S':
                $paper_wieght = '38';
                break;
            case 'P':
                $paper_wieght = '50';
                break;
        }

        switch ($jog) {
            case 'H':
                $jog_to = 'head';
                break;
            case 'F':
                $jog_to = 'foot';
                break;
        }

        switch ($carton_code) {
            case 'S':
                $carton_size = 'small';
                $paper_size  = 'small';
                break;
            case 'L':
                $carton_size = 'large';
                $paper_size  = 'large';
                break;
            case 'M':
                $carton_size = 'large';
                $paper_size  = 'small';
                break;
        }

        $form_configuration                  = [
            'configuration' => $config,
            'paper_wieght'  => $paper_wieght,
            'jog_to'        => $jog_to,
            'carton_size'   => $carton_size,
            'paper_size'    => $paper_size,
            'bind_type'     => $bind_type,
        ];

        $this->form_configuration            = $form_configuration;

        return $form_configuration;
    }

    public function delete_job()
    {
        $this->delete_form();
        $this->deleteFromDatabase('media_job');

        MediaFileSystem::delete($this->pdf_file);
        MediaFileSystem::delete($this->pdf_tmp_file);
        MediaFileSystem::delete($this->base_dir);
    }

    public function delete_form($form_number = '')
    {
        $this->deleteFromDatabase('form_data', $form_number);
        $this->deleteFromDatabase('form_data_count', $form_number);

        if ('' == $form_number) {
            $this->deleteFromDatabase('media_forms');
        }
    }

    private function deleteFromDatabase($table, $form_number = '')
    {
        $table_obj = $this->exp->table($table);
        if ('' != $form_number) {
            $table_obj->where('form_number', $form_number);
        }
        $table_obj->where('job_id', $this->job_id)->delete();
    }

    public function delete_xlsx()
    {
        $msg = null;
        if (true == $this->xlsx) {
            $msg = MediaFileSystem::delete($this->xlsx_directory);
            if (null === $msg) {
                self::set_exists(0, 'xlsx', $this->job_id);
                $this->xlsx = false;
            }
        }

        return $msg;
    }

    public function delete_zip()
    {
        $msg = null;
        if (true == $this->zip) {
            $msg = MediaFileSystem::delete($this->zip_directory);
            if (null === $msg) {
                self::set_exists(0, 'zip', $this->job_id);
                $this->zip = false;
            }
        }

        return $msg;
    }

    public function update_job_number($job_number)
    {
        $data             = ['job_number' => $job_number];
        $this->exp->table('media_job')->where('job_id', $this->job_id)->update($data);
        $this->job_number = $job_number;
        $this->getDirectories();
    }

    public function updateFormRow($id, $data)
    {
        $this->exp->table('form_data')->where('id', $id)->update($data);
    }

    public function getFormRow($id)
    {
        $row = $this->exp->table('form_data')->get($id);

        foreach ($row as $k => $o) {
            $res_array[$k] = $o;
        }

        return $res_array;
    }

    public function addFormRow($data)
    {
        $this->conn->query('INSERT INTO form_data', $data);

        $id = $this->conn->getInsertId();

        return $id;
    }

    public function deleteFormRow($id)
    {
        $this->conn->query('DELETE FROM  form_data WHERE id = ?', $id);
    }

    public function deleteSlipSheets()
    {
        $this->exp->table('form_data_count')->where('job_id', $this->job_id)->delete();
    }

    public function add_form_details($form_array)
    {
        $this->exp->table('media_forms')->insert($form_array);
    }

    public function add_form_data($form_number, $form_array)
    {
        //  $config = $form_array['details']['config'];
        $forms = $form_array['forms'];

        foreach ($forms as $letter => $row) {
            foreach ($row as $individual_part) {
                $individual_part['job_id']      = $this->job_id;
                $individual_part['form_letter'] = $letter;
                $individual_part['form_number'] = $form_number;
                $this->exp->table('form_data')->insert($individual_part);
            }
        }
    }

    public static function insertJobNumber($pdf_filename, $job_number)
    {
        global $explorer;
        $job_id       = null;
        $pdf_filename = basename($pdf_filename);

        $explorer->table('media_job')->insert([
            'job_number' => $job_number,
            'pdf_file'   => $pdf_filename,
        ]);

        $vares        = $explorer->table('media_job')->where('pdf_file = ?', $pdf_filename);
        foreach ($vares as $u) {
            $job_id = $u->job_id;
        }

        return $job_id;
    }

    public static function getJobNumber($pdf_filename, $job_number = null)
    {
        global $explorer;
        $job_id       = null;
        $pdf_filename = basename($pdf_filename);

        $val          = $explorer->table('media_job')->where('pdf_file = ?', $pdf_filename);
        // $val->where('pdf_file LIKE ', $pdf_filename); //->select('job_id');

        // dd($pdf_filename, $val);
        foreach ($val as $u) {
            $job_id = $u->job_id;
        }
        if (null !== $job_number) {
            if (null === $job_id) {
                $job_id = self::insertJobNumber($pdf_filename, $job_number);
            }
        }

        return $job_id;
    }
}
