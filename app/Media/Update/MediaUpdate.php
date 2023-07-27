<?php
/**
 * CWP Media tool
 */

namespace CWP\Media\Update;

/*
 * CWP Media tool
 */

use CWP\Bootstrap;
use CWP\Db\MediaMySQL;
use CWP\Db\MediaSqlite;
use CWP\Utils;
use Nette\Database\Connection;
use Nette\Database\Helpers;
use Nette\Utils\FileSystem;

class MediaUpdate
{
    public $table_name;

    public $refresh    = false;

    public $conn;
    public $conf;

    public object $dbClassObj;

    public function __construct($db_conn)
    {
        $this->conn       = $db_conn;

        if ('mysql' == Bootstrap::$CONFIG['db']['type']) {
            $this->dbClassObj =  new MediaMySQL($this, $db_conn);
        }
        if ('sqlite' == Bootstrap::$CONFIG['db']['type']) {
            $this->dbClassObj =  new MediaSqlite($this, $db_conn);
        }
    }

    public static function createDatabase()
    {
        if (!file_exists(__SQLITE_DATABASE__)) {
            FileSystem::createDir(__DATABASE_ROOT__, 777);
            if ('mysql' == Bootstrap::$CONFIG['db']['type']) {
                touch(__SQLITE_DATABASE__);
            }
            $connection       = new Connection(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);
            $_default_sql_dir = FileSystem::normalizePath(__DEFAULT_TABLES_DIR__);
            $file_tableArray  = Utils::get_filelist($_default_sql_dir, 'cwp_table.*)\.(sql', 0);
            foreach ($file_tableArray as $k => $sql_file) {
                $table_name = str_replace('cwp_table_', '', basename($sql_file, '.sql'));
                $connection->query('drop table if exists '.$table_name);
                Helpers::loadFromFile($connection, $sql_file);
            }

            Helpers::loadFromFile($connection, $_default_sql_dir.'/cwp_data.sql');

            return true;
        }

        return false;
    }
}
