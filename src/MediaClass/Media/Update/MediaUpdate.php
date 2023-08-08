<?php
/**
 * CWP Media tool
 */

namespace CWP\Media\Update;

/*
 * CWP Media tool
 */

use CWP\Db\MediaMySQL;
use CWP\Db\MediaSqlite;
use CWP\Media\Bootstrap;
use CWP\Media\Media;

class MediaUpdate
{
    public $table_name;

    public $refresh = false;

    public $conn;
    public $conf;

    public object $dbClassObj;

    public function __construct()
    {
        //$this->conn = Media::$connection;

        if ('mysql' == Bootstrap::$CONFIG['db']['type'])
        {
            $this->dbClassObj = new MediaMySQL($this);
        }
        if ('sqlite' == Bootstrap::$CONFIG['db']['type'])
        {
            $this->dbClassObj = new MediaSqlite($this);
        }
    }
}
