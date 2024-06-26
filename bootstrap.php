<?php
/**
 * CWP Media Load Flag Creator.
 */

use Camoo\Config\Config;
use CWP\Core\Bootstrap;
use CWP\Core\Media;
use CWP\Core\MediaStopWatch;
use PHLAK\Stash;
use UTM\Utm;
use Dotenv\Dotenv;

class EnvLoader
{
    public static function LoadEnv($directory)
    {

        $fp = @fsockopen('tcp://127.0.0.1', 9912, $errno, $errstr, 1);
        if (!$fp) {
            $env_file = '.env';
        } else {
            $env_file = '.env-server';
        }

        return Dotenv::createUnsafeImmutable($directory, $env_file);
    }
}

define('__COMPOSER_DIR__', __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'vendor');
define('__CWP_SOURCE__', __PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'src');
define('__CMD_ROOT__', __CWP_SOURCE__.\DIRECTORY_SEPARATOR.'Cmd');
define('__CMD_CONFIG__', __CMD_ROOT__.\DIRECTORY_SEPARATOR.'Config');

define('__CONFIG_ROOT__', __CWP_SOURCE__.\DIRECTORY_SEPARATOR.'Configuration');
define('__ERROR_LOG_DIRECTORY__', __CWP_SOURCE__.\DIRECTORY_SEPARATOR.'var'.\DIRECTORY_SEPARATOR.'logs');

define('__CACHE_DIR__', __CWP_SOURCE__.\DIRECTORY_SEPARATOR.'var'.\DIRECTORY_SEPARATOR.'cache');
define('__STASH_DIR__', __CACHE_DIR__.\DIRECTORY_SEPARATOR.'stash'.\DIRECTORY_SEPARATOR);

define('__TPL_CACHE_DIR__', __CACHE_DIR__.\DIRECTORY_SEPARATOR.'template'.\DIRECTORY_SEPARATOR);
define('__SCRIPT_NAME__', basename($_SERVER['PHP_SELF'], '.php'));
require __COMPOSER_DIR__.\DIRECTORY_SEPARATOR.'autoload.php';

$boot = new Bootstrap(new Config(__PUBLIC_ROOT__.\DIRECTORY_SEPARATOR.'config.ini'));
Utm::LoadEnv();

new Utm();
ini_set('max_execution_time', '600');
// register_shutdown_function([MediaStopWatch::class, 'flushLogs']);

$stash = Stash\Cache::file(function (): void {
    $this->setCacheDir(__STASH_DIR__);
});

Media::$Stash = $stash;
