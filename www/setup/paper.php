<?php

use CWP\HTML\HTMLDisplay;
use CWP\Spreadsheet\Media\MediaXLSX;
/**
 * CWP Media tool
 */

require_once '.config.inc.php';



foreach ($_REQUEST as $key => $data_array) {
    if($key == "submit") {
        continue;
    }
    $field_array    = [];
    foreach($data_array as $field => $value)
    {
        if($value == ""){
            $value          = null;
        }
        $field_array[$field] = $value;
    }

    $count[]            = $explorer->table('paper_count')->where('id', $key)->update($field_array);

}

dd($count);