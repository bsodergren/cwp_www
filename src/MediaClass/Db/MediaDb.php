<?php
/**
 * CWP Media tool
 */

namespace CWP\Db;

use CWP\Media\Media;

/**
 * CWP Media tool.
 */
class MediaDb
{
    public $conn;
    public $tableTmpName = 'sqlb_temp_table_1';

    public function __construct($class)
    {
        $this->conn = $class;
    }

public function __call($method,$args)
{
   // $method = str_replace('_', '', $method);
    if(method_exists($this,$method)){
        $this->$method(...$args);

    }
    dd("Nope");

}


}
