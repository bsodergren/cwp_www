<?php

use Nette\Utils\FileSystem;

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
 
    protected $exp;
    protected $conn;
    private $mediaLoc;
    public $MediaArray = [];

    public $job_id;
    public $pdf_file;
    public $job_number;
    public $xlsx;
    public $zip;
    public $location;

    public function __construct($MediaDB = '')
    {
        global $connection;
        global $explorer;
        $this->conn = $connection;
        $this->exp = $explorer;

        if (is_object($MediaDB)) {
            $array = get_object_vars($MediaDB);
            unset($MediaDB);
            $MediaDB = $array;
        }


        if (is_array($MediaDB)) {
            $this->job_id  = (empty($MediaDB['job_id'])) ? '' : $MediaDB['job_id'];
            $this->pdf_file  = (empty($MediaDB['pdf_file'])) ? '' : $MediaDB['pdf_file'];
            $this->job_number  = (empty($MediaDB['job_number'])) ? '' : $MediaDB['job_number'];
            $this->xlsx  = (empty($MediaDB['xlsx_exists'])) ? '' : $MediaDB['xlsx_exists'];
            $this->zip  = (empty($MediaDB['zip_exists'])) ? '' : $MediaDB['zip_exists'];
            $this->location  = (empty($MediaDB['base_dir'])) ? '' : $MediaDB['base_dir'];

            $this->getDirectories();
        }
    }


    public function excelArray()
    {
        //global $explorer;


        $form_config = $this->get_drop_details();

        foreach ($form_config as $form_number => $vars) {


            $total_back_peices = 0;

            $prev_form_letter = "";

            $sort = array("SORT_FORMER" => 1, "SORT_LETTER" => 1, "SORT_PUB" => 1);

            $result = $this->get_drop_form_data($form_number, $sort);

            foreach ($result as $row_id => $form_row) {

                $current_form_letter = $form_row["form_letter"];

                if ($prev_form_letter != $current_form_letter) {
                    $total_back_peices = 0;
                    $prev_form_letter = $current_form_letter;
                }

                if ($form_row["former"] == "") {
                    $form_row["former"] = "Front";
                }

                if (!isset($this->MediaArray[$form_number]["bind"])) {
                    $this->MediaArray[$form_number] = array(
                        "bind" => $vars["bind"],
                        "config" => $vars["config"],
                        "job_number" => $form_row["job_number"],
                        "pdf_file" => $form_row["pdf_file"],
                        "job_id" => $media->job_id
                    );
                }


                if ($form_row["former"] == "Back") {
                    $total_back_peices = $total_back_peices + $form_row["count"];
                    $this->MediaArray[$form_number][$form_row["former"]][$form_row["form_letter"]][99] = array(
                        "market" => $form_row["market"],
                        "pub" => $form_row["pub"],
                        "count" => $total_back_peices,
                        "ship" => $form_row["ship"],
                        "job_number" => $form_row["job_number"],
                        "former" => $form_row["former"],
                        "face_trim" => $form_row["face_trim"],
                        "no_bindery" => $form_row["no_bindery"]
                    );
                } else {
                    $this->MediaArray[$form_number][$form_row["former"]][$form_row["form_letter"]][] = array(
                        "market" => $form_row["market"],
                        "pub" => $form_row["pub"],
                        "count" => $form_row["count"],
                        "ship" => $form_row["ship"],
                        "job_number" => $form_row["job_number"],
                        "former" => $form_row["former"],
                        "face_trim" => $form_row["face_trim"],
                        "no_bindery" => $form_row["no_bindery"]
                    );
                }
            }
        }

        $this->MediaArray;
    }



    private function getDirectories()
    {

        $this->mediaLoc = new MediaFileSystem($this->pdf_file, $this->job_number);

        $this->base_dir = $this->mediaLoc->getDirectory();

        $this->pdf_fullname = $this->mediaLoc->getFilename('pdf');
        $this->pdf_tmp_file = $this->pdf_fullname . '.~qpdf-orig';

        $this->xlsx_directory = $this->mediaLoc->getDirectory('xlsx');
        $this->zip_directory = $this->mediaLoc->getDirectory('zip');
        $this->zip_file = $this->mediaLoc->getFilename('zip');
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
        if ($value == 0) {
            $value = '';
        }
        $result = $explorer->table('media_job')->where('job_id', $job_id)->update([$field . '_exists' => $value]);
    }

    public static function get_exists($field, $job_id)
    {
        global $explorer;
        $result = $explorer->table('media_job')->select($field . '_exists')->where('job_id', $job_id);
        $exists = $result->fetch();
        $var_name = $field . '_exists';
        return Utils::toint($exists->$var_name);
    }


    public function number_of_forms()
    {

        return $this->exp->table("media_forms")->where("job_id", $this->job_id)->count('*');
    }



    public function get_max_drop_forms()
    {

        $sql = "SELECT DISTINCT(`form_number`) as max FROM `media_forms` WHERE `job_id` = " . $this->job_id . "  ORDER BY `max` DESC limit 1";
        $result = $this->conn->fetch($sql);
        return $result["max"];
    }

    public function get_first_form()
    {
        $sql = "SELECT `form_number` as max FROM `media_forms` WHERE `job_id` = " . $this->job_id . " ORDER BY `max` ASC limit 1";
        $result = $this->conn->fetch($sql);

        return $result["max"];
    }



    public  function get_drop_details($form_number = '')
    {

        $form = '';

        if ($form_number == true) {
            $form = " and `form_number`= " . $form_number;
        }

        $sql = "SELECT `bind`,`config`,`form_number` FROM `media_forms` WHERE `job_id` = " . $this->job_id . $form;

        $result = $this->conn->query($sql);

        $form_config = array();

        foreach ($result as $idx => $data) {
            $form_config[$data["form_number"]] = array("bind" => $data["bind"], "config" => $data["config"]);
        }

        $this->form_config = $form_config;
        return $form_config;
    }

    public  function get_drop_form_data($form_number = '', $sort = array())
    {
        $add = '';

        if ($form_number == true) {
            $FORM_SEQ = " and `f`.`form_number` = " . $form_number;
        }

        if (array_key_exists("SORT_LETTER", $sort)) {
            if (isset($sort_query)) {
                $add = $sort_query . ", ";
            }
            $sort_query = " `f`.`form_letter` ASC ";
        }

        if (array_key_exists("SORT_NUMBER", $sort)) {
            if (isset($sort_query)) {
                $add = $sort_query . ", ";
            }
            $sort_query =  $add . " `f`.`form_number` ASC ";
        }

        if (array_key_exists("SORT_PUB", $sort)) {
            if (isset($sort_query)) {
                $add = $sort_query . ", ";
            }
            $sort_query = $add . " `f`.`pub` ASC ";
        }

        if (array_key_exists("SORT_FORMER", $sort)) {
            if (isset($sort_query)) {
                $add = $sort_query . ", ";
            }
            $sort_query = $add . " `f`.`former` DESC ";
        }

        if (isset($sort_query)) {
            $sort_query = " ORDER BY " . $sort_query;
        } else {
            $sort_query = '';
        }

        $sql = "SELECT `f`.`id`,`f`.`job_id`,`f`.`form_number`,`f`.`form_letter`,`f`.`market`,`f`.`pub`,`f`.`count`,`f`.`ship`,`f`.`former`,`f`.`face_trim`,`f`.`no_bindery`,`m`.`job_number`, `m`.`pdf_file` FROM `form_data` f, `media_job` m WHERE ( `f`.`job_id` = " . $this->job_id . " and `m`.`job_id` = " . $this->job_id . $FORM_SEQ . " ) " . $sort_query;


        $result = $this->conn->fetchAll($sql);

        return $result;
    }


    public  function get_Job($job_id)
    {
        $table = $this->exp->table("media_job");
        return $table->get($job_id);
    }

    public  function get_form_configuration($data)
    {
        $config = $data["config"];
        list($bind_type, $jog, $carton_code) = str_split($data['bind']);


        switch ($bind_type) {
            case "S":
                $paper_wieght = "38";
                break;
            case "P":
                $paper_wieght = "50";
                break;
        }

        switch ($jog) {
            case "H":
                $jog_to = "head";
                break;
            case "F":
                $jog_to = "foot";
                break;
        }

        switch ($carton_code) {
            case "S":
                $carton_size = "small";
                $paper_size = "small";
                break;
            case "L":
                $carton_size = "large";
                $paper_size = "large";
                break;
            case "M":
                $carton_size = "large";
                $paper_size = "small";
                break;
        }

        $form_configuration = array(
            "configuration" => $config,
            "paper_wieght" => $paper_wieght,
            "jog_to" => $jog_to,
            "carton_size" => $carton_size,
            "paper_size" => $paper_size,
            "bind_type" => $bind_type
        );

        $this->form_configuration = $form_configuration;
        return $form_configuration;
    }



    public function delete_job()
    {
        $this->delete_form();
        $this->deleteFromDatabase('media_job');

        if (file_exists($this->pdf_file)) {
            FileSystem::delete($this->pdf_file);
        }

        if (file_exists($this->pdf_tmp_file)) {
            FileSystem::delete($this->pdf_tmp_file);
        }

        if (is_dir($this->base_dir)) {
            FileSystem::delete($this->base_dir);
        }
    }

    public function delete_form($form_number='')
    {
        $this->deleteFromDatabase('form_data',$form_number);
        if($form_number == '')
        {
            $this->deleteFromDatabase('media_forms');
        }
    }

    private function deleteFromDatabase($table,$form_number='')
    {

        $table_obj = $this->exp->table($table);
            if($form_number != ''){
                $table_obj->where('form_number', $form_number);
            }
            $table_obj->where('job_id', $this->job_id)->delete();
    }

    public function delete_xlsx()
    {


        if ($this->xlsx == true) {
            if (is_dir($this->xlsx_directory)) {
                FileSystem::delete($this->xlsx_directory);
            }
        }
        Media::set_exists(0, "xlsx", $this->job_id);
        $this->xlsx = false;
    }

    public function delete_zip()
    {

        if ($this->zip == true) {
            if (is_dir($this->zip_directory)) {
                FileSystem::delete($this->zip_directory);
            }
        }
        Media::set_exists(0, "zip", $this->job_id);
        $this->zip = false;
    }


    public function update_job_number($job_number)
    {
        $data = array('job_number' => $job_number);
        $this->exp->table("media_job")->where('job_id', $this->job_id)->update($data);
        $this->job_number = $job_number;
        $this->getDirectories();
    }


    public function updateFormRow($id, $data)
    {
        $this->exp->table("form_data")->where('id', $id)->update($data);
    }

    public function getFormRow($id)
    {
        $row =  $this->exp->table('form_data')->get($id);

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


    public function add_form_details($form_array)
    {
        $this->exp->table("media_forms")->insert($form_array);
    }

    public function add_form_data($form_number, $form_array)
    {

        //  $config = $form_array['details']['config'];
        $forms = $form_array['forms'];


        foreach ($forms as $letter => $row) {
            foreach ($row as $individual_part) {

                $individual_part['job_id'] = $this->job_id;
                $individual_part['form_letter'] = $letter;
                $individual_part['form_number'] = $form_number;

                // dump($individual_part);

                $this->exp->table("form_data")->insert($individual_part);
            }
        }
    }
}



class MediaImport extends Media
{

    public $job_id = '';
    public $status = '';
    protected $conn;
    protected $exp;

    public function __construct($pdf_uploaded_file = "", $job_number = 110011,$update_form='')
    {

        global $connection;
        global $explorer;

        $this->conn = $connection;
        $this->exp = $explorer;

        $job_id = '';

        //$media = $explorer->table("media_job");

        $pdf_filename = basename($pdf_uploaded_file);

        $val = $this->exp->table("media_job")->where('pdf_file', $pdf_filename)->select('job_id');
        foreach ($val as $u) {
            $this->job_id = $u->job_id;
        }



        if ($this->job_id == '') {
            $this->exp->table("media_job")->insert([
                'job_number' => $job_number,
                'pdf_file' => $pdf_filename,
            ]);
            $this->job_id = $this->exp->getInsertId();
        }


        //$pdf = process_pdf($pdf_uploaded_file, $this->job_id);


        $pdfObj = new PDFImport($pdf_uploaded_file, $this->job_id,$update_form);
        $pdf = $pdfObj->form;
        if (count($pdf) < 1) {
            return 0;
        }

        $keyidx = array_key_first($pdf);


        $this->exp->table('media_job')->where('job_id', $this->job_id)->update(['close' => $pdf[$keyidx]['details']['product']]);

        foreach ($pdf as $form_number => $form_info) {
            $this->add_form_details($form_info['details']);
            $this->add_form_data($form_number, $form_info);
        }

        $this->status = 1;
    }


}
