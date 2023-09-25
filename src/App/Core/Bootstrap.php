<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Core;

use Camoo\Config\Config;
use Nette\Utils\FileSystem;

class Bootstrap
{
    public static $CONFIG;
    public object $Config;

    public function __construct(Config $Config)
    {
        $this->Config = $Config;
        self::$CONFIG = $Config->all();
        $this->define('__DEBUG__', $this->isDebugSet());
        $this->definePath('__BIN_DIR__', $this->getUsrBin());
        $this->define('__URL_PATH__', $this->getURL());
        $this->define('__HOME__', \dirname($_SERVER['DOCUMENT_ROOT'], 2));
    }

    public function directory($path)
    {
        $path = FileSystem::normalizePath($path);
        if (!is_dir($path)) {
            FileSystem::createDir($path, 511);
        }

        return $path;
    }

    public function define($const, $value)
    {
        if (!\defined($const)) {
            \define($const, $value);
        }
    }

    public function definePath($const, $value)
    {
        $value = FileSystem::normalizePath($value);
        $this->define($const, $value);
    }

    public function getDatabase()
    {
        if ('mysql' == $this->Config['db']['type']) {
            $this->definePath('__SQLITE_DATABASE__', __DATABASE_ROOT__.\DIRECTORY_SEPARATOR.'using_mysql.db');
            $database_files = __SQL_CONFIG_DIR__.\DIRECTORY_SEPARATOR.'mysql';
            $database_dsn = 'mysql:host='.$this->Config['db']['host'].';dbname='.$this->Config['db']['dbname'];
        } else {
            $this->definePath('__SQLITE_DATABASE__', __DATABASE_ROOT__.\DIRECTORY_SEPARATOR.'cwp_sqlite.db');
            $database_files = __SQL_CONFIG_DIR__.\DIRECTORY_SEPARATOR.'sqllite';
            $database_dsn = 'sqlite:'.__SQLITE_DATABASE__;
        }

        $this->define('__DATABASE_DSN__', $database_dsn);
        $this->define('DB_DATABASE', $this->Config['db']['dbname']);
        $this->define('DB_USERNAME', $this->Config['db']['username']);
        $this->define('DB_PASSWORD', $this->Config['db']['password']);
        $this->definePath('__DEFAULT_TABLES_DIR__', $database_files);
    }

    private function getUsrBin()
    {
        if (!\array_key_exists('OS', $_SERVER)) {
            return '/usr/bin';
        }

        return __PROJECT_ROOT__.\DIRECTORY_SEPARATOR.'bin';
    }

    private function isDebugSet()
    {
        return $this->Config['application']['debug'];
    }

    private function getURL()
    {
        return $this->Config['server']['url_root'];
    }
}
