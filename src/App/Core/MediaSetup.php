<?php
/**
 * CWP Media Load Flag Creator.
 */

namespace CWP\Core;

use CWP\HTML\HTMLDisplay;
use CWP\Template\Template;
use CWP\Utils\Utils;
use Nette\Database\Connection;
use Nette\Database\Helpers;
use Nette\Utils\FileSystem;

class MediaSetup
{
    public function __construct()
    {
        if (true == self::firstRun()) {
            self::create();
            self::footer(0);
        }
    }

    public static function firstRun()
    {
        if ('mysql' == Bootstrap::$CONFIG['db']['type']) {
            $res = Media::$connection->query('SHOW TABLES');
            if (0 == $res->getRowCount()) {
                return true;
            }

            return false;
        }
        if (file_exists(__SQLITE_DATABASE__)) {
            return false;
        }

        return true;
    }

    public static function create()
    {
        self::header('Creating new Database');

        if ('mysql' != Bootstrap::$CONFIG['db']['type']) {
            FileSystem::createDir(__DATABASE_ROOT__, 777);
        }

        //  $connection = new Connection(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);
        $_default_sql_dir = FileSystem::normalizePath(__DEFAULT_TABLES_DIR__);
        $file_tableArray = Utils::get_filelist($_default_sql_dir, 'cwp_table.*)\.(sql');
        foreach ($file_tableArray as $k => $sql_file) {
            $table_name = str_replace('cwp_table_', '', basename($sql_file, '.sql'));
            self::message('Loading schema for '.$table_name);
            Media::$connection->query('drop table if exists '.$table_name);
            Helpers::loadFromFile(Media::$connection, $sql_file);
        }
        self::message('Loading default Data');

        Helpers::loadFromFile(Media::$connection, $_default_sql_dir.'/cwp_data.sql');

        return true;
    }

    public static function header($msg)
    {
        // new HTMLDisplay();
        echo Template::GetHTML('setup/header', []);
        echo HTMLDisplay::pushhtml('setup/msg', ['TEXT' => $msg]);
    }

    public static function footer($time = 3)
    {
        self::reload($time);
        echo Template::GetHTML('setup/footer', []);
    }

    public static function message($msg)
    {
        echo HTMLDisplay::pushhtml('setup/file_msg', ['TEXT' => $msg]);
    }

    public static function reload($time = 3)
    {
        echo HTMLDisplay::JavaRefresh('/index.php', $time);
        exit;
    }
}
