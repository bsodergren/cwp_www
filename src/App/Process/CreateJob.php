<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Process;

use CWP\Core\Media;
use CWP\Utils\Utils;
use Nette\Utils\FileSystem;
use CWP\Filesystem\MediaFileSystem;
use CWP\Spreadsheet\Media\MediaXLSX;

class CreateJob extends MediaProcess
{
    public $form_number;

    public $job_id;

    public object $fs;

    public $addFormPage = "/create/addForm.php";
    public $addRowPage = "/create/addRow.php";

    public $dateMap = [ 'JAN' => "01",'FEB' => "02",'MAR' => "03",'APR' => "04",'MAY' => "05",'JUN' => "06",'JUL' => "07",'AUG' => "08",'SEP' => "09",'OCT' => "10",'NOV' => "11",'DEC' => "12", ];
    public function __construct()
    {
        $this->fs = new MediaFileSystem();

    }
    public function run($req)
    {

        $method =  $req['action'];
        // if(method_exists($this, $method)) {
        $this->$method($req);
        //   }

    }

    public function __call($name, $arguments)
    {
        dd($name, $arguments);
    }
    public function addForm($req)
    {
        $media = new Media();
        $details['config']      = $req['config'];
        $details['bind']        = $req['bind'];

        $details['count']       = Utils::toint($req['form_count']);

        $details['product']     = $req['product'];
        $details['job_id']      = $req['job_id'];
        $details['form_number'] = $req['form_number'];
        $return = $media->add_form_details($details);
        $this->url = $this->addFormPage."?job_id=".$req['job_id'];


    }

    public function addRow($req)
    {


        $job_id = $req["job_id"];
        $form_number = $req["form_number"];
        $letter = strtoupper($req["letter"]);
        $market = strtoupper($req["market"]);
        $pub = strtoupper($req["publication"]);
        $ship = strtoupper($req["destination"]);
        $count = $req["count"];


        $data = [
            'form_letter' => $letter,
            'job_id' => $job_id,
            'form_number' => $form_number,
            'original' => $market.' '.$pub.' '.$count.' '.$ship,
            'market'   => $market,
            'pub'      => $pub,
            'count'    => $count,
            'ship'     => $ship
        ];


        $query = 'INSERT INTO `form_data` ?';

        Media::$connection->query($query, $data);
        Media::$connection->getInsertId();
        $this->url = $this->addRowPage."?job_id=".$job_id."&form_number=".$form_number;
    }

    public function createjob($data)
    {
        global $auth;
        $pdf_filename = 'Created By '.$data['username'];
        $job_number = $data['job_number'];

        $this->job_id = Media::getJobNumber($pdf_filename, $job_number);
        if (null !== $this->job_id) {
            $this->url = $this->addFormPage."?job_id=".$this->job_id;
            return $this->job_id;
        }

        [$month,$year,$drop] = explode(" ", $data['job_name']);
        $nummonth = $this->dateMap[strtoupper($month)];
        $shortyear = substr($year, 2, 2);
        $shortdrop = substr($drop, 0, 1);
        $mediaDrop = $nummonth.$shortyear."-".$shortdrop."_Runsheets_Itasca";

        $dirArray = [__FILES_DIR__,__MEDIA_FILES_DIR__,$job_number,$mediaDrop];
        $directory = implode(DIRECTORY_SEPARATOR, $dirArray);
        $directory = FileSystem::platformSlashes($directory);
        $directory = FileSystem::normalizePath($directory);

        $this->fs->createFolder($directory);


        $query = 'INSERT INTO `media_job` ?';

        Media::$connection->query($query, [
            'job_number' => $job_number,
            'close' => $data['job_name'],
            'pdf_file' => $pdf_filename,
            'base_dir' => $directory,
        ]);

        $this->job_id = Media::$connection->getInsertId();
        $this->url = $this->addFormPage."?job_id=".$this->job_id;
    }

}
