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

class MediaUpdate
{
    public $table_name;

    public $refresh = false;

    public $conn;
    public $conf;

    public object $dbClassObj;

    public function __construct($db_conn)
    {
        $this->conn = $db_conn;

        if ('mysql' == Bootstrap::$CONFIG['db']['type']) {
            $this->dbClassObj = new MediaMySQL($this, $db_conn);
        }
        if ('sqlite' == Bootstrap::$CONFIG['db']['type']) {
            $this->dbClassObj = new MediaSqlite($this, $db_conn);
        }
    }
}
