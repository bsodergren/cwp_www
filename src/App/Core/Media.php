<?php
/**
 * CWP Media Load Flag Creator
 */

namespace CWP\Core;

use CWP\Utils\Utils;
use CWP\JobCreator\Creator;
use CWP\Media\MediaPublication;
use CWP\Filesystem\MediaFileSystem;
use CWP\Filesystem\Driver\MediaLocal;
use CWP\Filesystem\Driver\MediaDropbox;
use CWP\Filesystem\Driver\MediaGoogleDrive;

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
    public static $explorer; // UPDATEME // UPDATEME

    public static $connection;

    public static $MySQL;
    public static $DB;


    public static $VersionUpdate;

    public static $CurrentVersion;

    public static $MediaAppUpdater;

    public static $Dropbox = false;

    public static $Google = false;
    public static $FileDriver;
    public static $Stash;
    public static $Obj;
    public static $Tpl;



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

    public $form_parts;

    public $xlsx_directory;

    public $zip_directory;

    public $zip_file;

    public $form_config;

    public $location;


    public static $pageType = ['2+2 pgs 4 out','2+4 pgs 2 out','4 pgs 4 out','4+2 pgs 2 out','6 pgs 2 out','4+4 pgs 2 out','8 pgs 2 out' ];
    public static $bindType = ['PFL', 'PFM', 'PFS', 'SHS', 'PHL', 'PHM', 'PFS'];


    public function __construct($MediaDB = '')
    {
        if (\is_object($MediaDB)) {
            $array = get_object_vars($MediaDB);
            unset($MediaDB);
            $MediaDB = $array;
        }

        if (\is_array($MediaDB)) {
            $this->job_id = (empty($MediaDB['job_id'])) ? '' : $MediaDB['job_id'];
            $this->pdf_file = (empty($MediaDB['pdf_file'])) ? '' : $MediaDB['pdf_file'];
            $this->job_number = (empty($MediaDB['job_number'])) ? '' : $MediaDB['job_number'];
            $this->xlsx = (empty($MediaDB['xlsx_exists'])) ? '' : $MediaDB['xlsx_exists'];
            $this->zip = (empty($MediaDB['zip_exists'])) ? '' : $MediaDB['zip_exists'];
            $this->location = (empty($MediaDB['base_dir'])) ? '' : $MediaDB['base_dir'];
            MediaStopWatch::lap("getDirectories Media", '', "Media Class");
            $this->getDirectories();
            MediaStopWatch::lap("getDirectories Media", '', "Media Class");
        }
    }

    private function getSectionLetter($section_array)
    {
        return $section_array['form_letter'];
    }

    private function getSectionFormer($section_array)
    {
        return $section_array['former'];
    }

    private function getSectionPdfFile($section_array)
    {
        return $section_array['pdf_file'];
    }

    private function getSectionJobID($section_array)
    {
        return $section_array['job_id'];
    }

    private function getSectionArray($section_array)
    {
        $bind = $this->form_parts['details']['bind'];
        $partArray['bind'] = $bind;

        foreach ($section_array as $key => $value) {
            switch ($key) {
                case 'id':
                    $partArray['form_id'] = $value;
                    break;
                case 'pdf_file':
                    break;
                case 'job_number':
                    $this->form_parts['details']['job_number'] = $value;
                    //     $partArray[$key] = $value;
                    //     break;
                    // case 'former':
                    // case 'form_letter':
                    //     $$key = $value;
                    //     $partArray[$key] = $value;
                    //     break;
                    break;
                case 'pub':
                    $trimData = MediaPublication::getTrimData($value, $bind);
                    foreach ($trimData as $tKey => $tValue) {
                        switch ($tKey) {
                            case 'pub':
                            case 'bind':
                                break;
                            default:
                                $partArray[$tKey] = $tValue;
                                break;
                        }
                    }

                    // no break
                default:
                    $partArray[$key] = $value;
                    break;
            }
        }

        return $partArray;
    }

    public function getMediaJob($formNumber = null)
    {
        $job_config = $this->getDropDetails($formNumber);
        foreach ($job_config as $form_number => $form_details) {
            $sort = ['SORT_LETTER' => 'ASC'];
            $result = $this->getFormDrops($form_number, $sort);
            $this->form_parts['details'] = $form_details;

            foreach ($result as $_ => $section_array) {
                $former = $this->getSectionFormer($section_array);
                $form_letter = $this->getSectionLetter($section_array);
                $this->form_parts['details']['job_id'] = $this->getSectionJobID($section_array);
                $this->form_parts['details']['pdf_file'] = $this->getSectionPdfFile($section_array);
                $this->form_parts['forms'][$former][$form_letter][] = $this->getSectionArray($section_array);
            }

            $job_forms[$form_number] = $this->form_parts;
            $this->form_parts = [];
        }

        $this->MediaArray = $job_forms;
        $this->combineBack();

        return $this->MediaArray;
    }

    private function combineBack()
    {
        foreach ($this->MediaArray as $form_number => $form_details) {
            $combinded = [];
            $replace = [];
            $count = 0;
            if (\array_key_exists('Back', $form_details['forms'])) {
                // $count = 0;
                $back_forms = $form_details['forms']['Back'];
                foreach ($back_forms as $letter => $parts) {
                    $combinded = [];
                    $count = 0;
                    foreach ($parts as $idx => $row) {
                        if (0 == $row['no_bindery']) {
                            $count = $row['count'] + $count;
                            $combinded[0] = $row;
                            $combinded[0]['count'] = $count;
                            $combinded[0]['market'] = __LANG_BINDERY;
                            $combinded[0]['ship'] = __LANG_BINDERY;
                        } else {
                            $combinded[] = $row;
                        }
                    }
                    $replace[$letter] = $combinded;
                }
                $this->MediaArray[$form_number]['forms']['Back'] = $replace;
            }
        }
    }

    public function excelArray($form_number = null)
    {
        return $this->getMediaJob($form_number);
    }

    public function getDirectories()
    {
        $this->mediaLoc = new MediaFileSystem($this->pdf_file, $this->job_number);

        $this->base_dir = $this->mediaLoc->getDirectory();

        $this->pdf_fullname = $this->mediaLoc->getFilename('pdf');
        $this->pdf_tmp_file = $this->pdf_fullname . '.~qpdf-orig';

        $this->xlsx_directory = $this->mediaLoc->getDirectory('xlsx');
        $this->zip_directory = $this->mediaLoc->getDirectory('zip');
        $this->zip_file = $this->mediaLoc->getFilename('zip');

        //     dump([$this->base_dir,$this->pdf_fullname,$this->pdf_tmp_file,$this->xlsx_directory,$this->zip_directory,$this->base_dir]);
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
        if (0 == $value) {
            $value = '';
        }
        $result = self::$explorer->table('media_job')->where('job_id', $job_id)->update([$field . '_exists' => $value]); // UPDATEME
    }

    public static function get_exists($field, $job_id)
    {
        $result = self::$explorer->table('media_job')->select($field . '_exists')->where('job_id', $job_id); // UPDATEME
        $exists = $result->fetch();
        $var_name = $field . '_exists';
        if (isset($exists->{$var_name})) {
            return Utils::toint($exists->{$var_name});
        }

        return null;
    }

    public function updatedForms()
    {
        return self::$explorer->table('media_forms')->where('job_id', $this->job_id)->where('updated', 1)->count('*'); // UPDATEME
    }

    public function number_of_forms()
    {
        return self::$explorer->table('media_forms')->where('job_id', $this->job_id)->count('*'); // UPDATEME
    }

    public function get_form_list()
    {
        $sql = 'SELECT form_number FROM media_forms WHERE `job_id` = ' . $this->job_id;

        return self::$connection->fetchAll($sql);
    }

    public function get_max_drop_forms()
    {
        $sql = 'SELECT DISTINCT(`form_number`) as max FROM `media_forms` WHERE `job_id` = ' . $this->job_id . '  ORDER BY `max` DESC limit 1';
        $result = self::$connection->fetch($sql);

        return $result['max'];
    }

    public function get_first_form()
    {
        $sql = 'SELECT `form_number` as max FROM `media_forms` WHERE `job_id` = ' . $this->job_id . ' ORDER BY `max` ASC limit 1';
        $result = self::$connection->fetch($sql);

        return $result['max'];
    }

    public function getDropDetails($form_number = '')
    {
        $form = '';

        if (true == $form_number) {
            $form = ' and `form_number`= ' . $form_number;
        }

        $sql = 'SELECT * FROM `media_forms` WHERE `job_id` = ' . $this->job_id . $form;

        $result = self::$connection->query($sql);

        $form_config = [];

        foreach ($result as $idx => $data) {
            $form_config[$data['form_number']] = ['bind' => $data['bind'], 'config' => $data['config'],
                'product' => $data['product'],
                'count' => $data['count']];
        }

        $this->form_config = $form_config;

        return $form_config;
    }

    private function sortFormDrops($field, $key, $sort, $sort_query = null)
    {
        $add = '';
        if (\array_key_exists($key, $sort)) {
            if (1 == $sort[$key]) {
                $sort[$key] = 'ASC';
            }
            if (0 == $sort[$key]) {
                $sort[$key] = 'DESC';
            }

            if (isset($sort_query)) {
                $add = $sort_query . ', ';
            }

            $sort_query = $add . ' `f`.`' . $field . '` ' . $sort[$key];
        }

        return $sort_query;
    }

    public function getFormDrops($form_number = '', $sort = [])
    {
        if (true == $form_number) {
            $FORM_SEQ = ' and `f`.`form_number` = ' . $form_number;
        }

        $sort_query = $this->sortFormDrops('form_letter', 'SORT_LETTER', $sort);
        $sort_query = $this->sortFormDrops('form_number', 'SORT_NUMBER', $sort, $sort_query);
        $sort_query = $this->sortFormDrops('pub', 'SORT_PUB', $sort, $sort_query);
        $sort_query = $this->sortFormDrops('former', 'SORT_FORMER', $sort, $sort_query);

        if (isset($sort_query)) {
            $sort_query = ' ORDER BY ' . $sort_query;
        } else {
            $sort_query = '';
        }

        $sql = 'SELECT `f`.`id`,`f`.`job_id`,`f`.`form_number`,`f`.`form_letter`,`f`.`market`,`f`.`pub`,`f`.`count`,`f`.`ship`,`f`.`former`,`f`.`face_trim`,`f`.`no_bindery`,`m`.`job_number`, `m`.`pdf_file` FROM `form_data` f, `media_job` m WHERE ( `f`.`job_id` = ' . $this->job_id . ' and `m`.`job_id` = ' . $this->job_id . $FORM_SEQ . ' ) ' . $sort_query;

        return self::$connection->fetchAll($sql);
    }

    public function get_form_configuration($data)
    {
        $config = $data['config'];
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
                $paper_size = 'small';
                break;
            case 'L':
                $carton_size = 'large';
                $paper_size = 'large';
                break;
            case 'M':
                $carton_size = 'large';
                $paper_size = 'small';
                break;
        }

        $form_configuration = [
            'configuration' => $config,
            'paper_wieght' => $paper_wieght,
            'jog_to' => $jog_to,
            'carton_size' => $carton_size,
            'paper_size' => $paper_size,
            'bind_type' => $bind_type,
        ];

        $this->form_configuration = $form_configuration;

        return $form_configuration;
    }

    public function delete_job()
    {
        $this->delete_form();
        $this->deleteFromDatabase('media_job');

        //   MediaFileSystem::delete($this->pdf_file);
        //   MediaFileSystem::delete($this->pdf_tmp_file);
        (new MediaFileSystem())->delete($this->base_dir);
    }

    public function delete_form($form_number = '')
    {
        $this->deleteFromDatabase('form_data', $form_number);
        $this->deleteFromDatabase('form_data_count', $form_number);

        if ('' == $form_number) {
            $this->deleteFromDatabase('media_forms');
        }
    }

    public function deleteFromDatabase($table, $form_number = '')
    {
        $table_obj = self::$explorer->table($table); // UPDATEME
        if ('' != $form_number) {
            $table_obj->where('form_number', $form_number);
        }
        $table_obj->where('job_id', $this->job_id);

        $count = $table_obj->delete();
    }

    public function delete_xlsx()
    {
        $msg = null;
        if (true == $this->xlsx) {
            $msg = (new MediaFileSystem())->delete($this->xlsx_directory);
            $this->deleteFromDatabase('form_data_count');
            // if (null === $msg) {
            self::set_exists(0, 'xlsx', $this->job_id);
            $this->xlsx = false;
            // }
        }

        return $msg;
    }

    public function delete_zip()
    {
        $msg = null;
        if (true == $this->zip) {
            $msg = (new MediaFileSystem())->delete($this->zip_directory);
            // if (null === $msg) {
            self::set_exists(0, 'zip', $this->job_id);
            $this->zip = false;
            // }
        }

        return $msg;
    }

    public function update_job_number($job_number)
    {
        $parts = explode(DIRECTORY_SEPARATOR,$this->base_dir);

       // $directory = str_replace($this->base_dir)

        $Close = last($parts);
        array_pop($parts);
        array_pop($parts);
        $parts[] = $job_number;
        $parts[] = $Close;
        $directory = implode(DIRECTORY_SEPARATOR,$parts);

        $data = ['job_number' => $job_number,
        'base_dir' => $directory ];

        self::$explorer->table('media_job')->where('job_id', $this->job_id)->update($data); // UPDATEME
        $this->job_number = $job_number;
        $this->getDirectories();
    }

    public function updateFormRow($id, $data)
    {
        self::$explorer->table('form_data')->where('id', $id)->update($data); // UPDATEME
    }

    public function getFormRow($id)
    {
        $row = self::$explorer->table('form_data')->get($id); // UPDATEME

        foreach ($row as $k => $o) {
            $res_array[$k] = $o;
        }

        return $res_array;
    }

    public function addFormRow($data)
    {
        self::$connection->query('INSERT INTO form_data', $data);

        return self::$connection->getInsertId();
    }

    public function deleteFormRow($id)
    {
        self::$connection->query('DELETE FROM  form_data WHERE id = ?', $id);
    }

    public function deleteSlipSheets()
    {
        self::$explorer->table('form_data_count')->where('job_id', $this->job_id)->delete(); // UPDATEME
    }

    public function add_form_details($form_array)
    {
        self::$explorer->table('media_forms')->insert($form_array); // UPDATEME
        self::formUpdated($form_array['form_number'], $form_array['job_id']);
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
                self::$explorer->table('form_data')->insert($individual_part); // UPDATEME
                $destination[] = ['name' => $individual_part['ship']];
                $market[] =  ['name' => $individual_part['market']];
                $publications[] =  ['name' => $individual_part['pub']];

            }
        }

        Creator::ImportJobDataFromPDF($destination, 'job_destination');
        Creator::ImportJobDataFromPDF($publications, 'job_publication');
        Creator::ImportJobDataFromPDF($market, 'job_market');
    }

    public static function insertJobNumber($pdf_filename, $job_number)
    {
        $locations     = new MediaFileSystem($pdf_filename, $job_number);
        $pdf_directory = $locations->getDirectory('pdf', false, true);
        //$base_dir = \dirname($pdf_filename, 2);
        // $pdf_filename = basename($pdf_filename);


        $query = 'INSERT INTO `media_job` ?';

        self::$connection->query($query, [
            'job_number' => $job_number,
            'pdf_file' => $pdf_filename,
            'base_dir' => $pdf_directory,
        ]);

        return self::$connection->getInsertId();
    }

    public static function getJobNumber($pdf_filename, $job_number = null)
    {
        $job_id = null;
        $pdf_filename = basename($pdf_filename);

        $job_table = self::$explorer->table('media_job'); // UPDATEME
        $job_table->where('pdf_file LIKE ?', "%" . $pdf_filename . "%");
        if (null !== $job_number) {
            $job_table->where('job_number = ?', $job_number);
        }

        foreach ($job_table as $u) {
            $job_id = $u->job_id;
        }

        return $job_id;
    }

    public static function getFileDriver()
    {
        if (self::$Dropbox) {
            self::$FileDriver = "Dropbox";
            return new MediaDropbox();
        }
        if (self::$Google) {
            self::$FileDriver = "Google Drive";
            return new MediaGoogleDrive();
        }
        self::$FileDriver = "Local FS";
        return new MediaLocal();
    }

    public static function formUpdated($form_number, $job_id)
    {
        $count = self::$explorer->table('media_forms')->where('job_id', $job_id)->where('form_number', $form_number) ->update(['updated' => 1]); // UPDATEME
    }

    public static function formUsed($form_number, $job_id)
    {
        $count = self::$explorer->table('media_forms')->where('job_id', $job_id)->where('form_number', $form_number) ->update(['updated' => 0]); // UPDATEME
    }

    public static function getFormUpdates($job_id)
    {
        return self::$explorer->table('media_forms')->where('job_id', $job_id)->where('updated', 1); // UPDATEME
    }




    public static function get($name, $timeout = 5, $closure)
    {
        return self::$Stash->remember($name, $timeout, $closure);
    }
}
