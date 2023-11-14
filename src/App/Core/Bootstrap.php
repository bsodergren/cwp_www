<?php
/**
 * CWP Media Load Flag Creator
 */

namespace CWP\Core;

use CWP\Core\Media;
use Camoo\Config\Config;
use Nette\Utils\FileSystem;

class Bootstrap
{
    public static $CONFIG;

    public object $Config;

    private $configkeys = [
        'application' => ['name','debug','register','authenticate'],
        'db' => ['type','path','dbname','host','username','password'],
        'email' => ['enable','imap','username','password','folder'],
        'server' => ['filedriver','media_files','file_root','url_root'],
        'google' => ['clientid','secret','token'],
    ];

    public function __construct(Config $Config)
    {
        $this->Config = $Config;
        self::$CONFIG = $Config->all();

        $this->checkConfigValues();
        $this->setFileDriver();
        $this->define('__DEBUG__', $this->isDebugSet());
        $this->definePath('__BIN_DIR__', $this->getUsrBin());
        $this->define('__URL_PATH__', $this->getURL());
        $this->define('__HOME__', $this-> homeDir());

    }

    public function homeDir()
    {
        if(isset($_SERVER['HOME'])) {
            $result = $_SERVER['HOME'];
        } else {
            $result = getenv("HOME");
        }

        if(empty($result) && function_exists('exec')) {
            if(strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                $result = exec("echo %userprofile%");
            } else {
                $result = exec("echo ~");
            }
        }

        return $result;
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

    private function setFileDriver()
    {
        Media::$Dropbox = false;
        Media::$Google = false;

        $filedriver = $this->Config['server']['filedriver'];

        if($filedriver == 'google') {
            Media::$Google = true;
        }
        if($filedriver == 'dropbox') {
            Media::$Dropbox = true;
        }
    }
    private function checkConfigValues()
    {
        $config = $this->Config->all();
        $exit = false;
        foreach($this->configkeys as $key => $sectionKeys) {

            if(!array_key_exists($key, $config)) {
                $exit = true;
                echo "Missing [$key] section <br>";
                continue;
            }
            foreach($sectionKeys as $skey) {
                if(!array_key_exists($skey, $config[$key])) {
                    $exit = true;
                    echo "Missing $skey under [$key]  <br>";
                    continue;
                }
            }
        }

        if($exit === true) {
            dd($this->configkeys, $config);
        }

    }
}
