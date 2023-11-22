<?php

namespace CWP\JobCreator;

use CWP\Core\Media;

class Creator
{



    public static function ImportJobDataFromPDF($array, $table)
    {
        foreach($array as $i => $data) {
            Media::$connection->query('INSERT IGNORE INTO '.$table, $data);
        }
    }
}
