<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Updater;

/*
 * CWP Media tool
 */

use CWP\Core\Bootstrap;
use CWP\Core\Media;
use CWP\Updater\Db\MediaMySQL;
use CWP\Updater\Db\MediaSqlite;

class MediaUpdate
{
    public $table_name;

    public $refresh = false;

    public $conn;
    public $conf;

    public object $dbClassObj;

    public function __construct()
    {
        // $this->conn = Media::$connection;

        if ('mysql' == Bootstrap::$CONFIG['db']['type']) {
            $this->dbClassObj = new MediaMySQL($this);
        }
        if ('sqlite' == Bootstrap::$CONFIG['db']['type']) {
            $this->dbClassObj = new MediaSqlite($this);
        }
    }
}
