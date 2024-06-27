<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Commands\Update;

use CWP\Core\Media;
use CWPCLI\Core\MediaCWP;
use CWPCLI\Utilities\Option;
use Symfony\Component\Console\Helper\ProgressBar;

trait Helper
{



    // "job_id" => 2
    // "job_number" => 231735
    // "pdf_file" => "/home/bjorn/www/cwp_www/www/files/Uploads/0422-A_RunSheets_Itasca.pdf"
    // "zip_exists" => null
    // "xlsx_exists" => "1"
    // "close" => "Apr 2022 A-Close"
    // "hidden" => 0
    // "base_dir" => "/home/bjorn/www/.temp/Media/231735/0422-A_RunSheets_Itasca"

    public function listJobs()
    {
        MediaCWP::$Table->setHeaders(["Job ID","Close","Job Number"]);
        $table = Media::$explorer->table('media_job'); // UPDATEME
        $results = $table->fetchAssoc('job_id');
        foreach($results as $row){
            MediaCWP::$Table->addRow([$row['job_id'],$row['close'],$row['job_number']]);
        }
         MediaCWP::$Table->render();



       return 1;
       // dd($results);

    }
}
