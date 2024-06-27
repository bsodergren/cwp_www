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


    public static function jsList($table)
    {
        // if(Media::$Stash->has($table)) {
        //     return Media::$Stash->get($table);
        // }

        $market_table = Media::$explorer->table($table); // UPDATEME
        foreach($market_table as $row) {
            $marketArray[] = $row->name;
        }

        $list = '"'. implode('","', $marketArray) .'"';

        Media::$Stash->put($table, $list, 15);
        return $list;
    }
}
