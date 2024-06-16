<?php
/**
 * CWP Media Load Flag Creator
 */

use CWP\Core\Media;
use CWP\Core\Bootstrap;
use CWP\Core\MediaSetup;
use CWP\Updater\DbUpdate;
use CWP\Database\Database;
use CWP\Core\MediaSettings;
use Nette\Database\Explorer;
use Nette\Database\Structure;
use Nette\Database\Connection;
use UTMTemplate\HTML\Elements;
use CWP\Updater\MediaAppUpdater;
use Nette\Caching\Storages\DevNullStorage;
use Nette\Database\Conventions\DiscoveredConventions;

$connection = new Connection(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);
$storage = new DevNullStorage();
$structure = new Structure($connection, $storage);
$conventions = new DiscoveredConventions($structure);
$explorer = new Explorer($connection, $structure, $conventions, $storage);

Media::$connection = $connection;
Media::$explorer = $explorer;
Media::$MySQL = new Database();

new MediaSetup();

$appUpdate = new MediaAppUpdater();

Media::$VersionUpdate = $appUpdate->isUpdate();

Media::$CurrentVersion = $appUpdate->current;
Media::$MediaAppUpdater = $appUpdate;

(new DbUpdate())->checkDbUpdates();

/*
 * CWP Media tool for load flags.
 */

// if (__DEBUG__ == 0) {
define('__USE_AUTHENTICATION__', Bootstrap::$CONFIG['application']['authenticate']);
// } else {
// define('__USE_AUTHENTICATION__', false);
// }

if (__USE_AUTHENTICATION__ == true) {
    $db = new PDO(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);
    $auth = new Delight\Auth\Auth($db);
    if (!defined('__AUTH__')) {
        define('__AUTH__', true);
    }

    if (__AUTH__ == true) {
        if (!$auth->isLoggedIn()) {
            echo Elements::JavaRefresh('/login/login.php', 0);
            exit;
        }
    }
}

define('APP_NAME', Bootstrap::$CONFIG['application']['name']);
define('APP_ORGANIZATION', 'cwp');
define('APP_OWNER', 'bjorn');
define('APP_DESCRIPTION', 'bjorn');

$results = Media::get('settings', 120, function () {
    $table = Media::$explorer->table('settings'); // UPDATEME
    $table->order('setting_type ASC');

    return $table->fetchAssoc('id');
});
if ($results) {
    foreach ($results as $k => $u) {
        $setting[$u['setting_cat']][$u['definedName']] = [
            'id' => $u['id'],
            'type' => $u['setting_type'],
            'value' => $u['setting_value'],
            'name' => $u['setting_name'],
            'description' => $u['setting_description'],
            'category' => $u['setting_cat'],
            'require' => $u['require'],
        ];
    }

    define('__SETTINGS__', $setting);
}

foreach ($setting['local'] as $key => $array) {
    if (null !== $array['require']) {
        if (array_key_exists($array['require'], $setting['local'])) {
            if (true == $setting['local'][$array['require']]['value']
            ) {
                $setting['local'][$key]['value'] = $array['value'];
            } else {
                $setting['local'][$key]['value'] = 0;
            }
        }
    }
}

foreach ($setting['lang'] as $key => $array) {
    if (!defined($key)) {
        define($key, $array['value']);
    }
}

foreach ($setting['local'] as $key => $array) {
    MediaSettings::configEmail($key);

    if (!defined($key)) {
        define($key, $array['value']);
    }
}

// $const = get_defined_constants(true);
Bootstrap::setFileDriver();

if (Media::$Google || Media::$Dropbox) {
    define('__FILES_DIR__', '');
} else {
    if (array_key_exists('media_files', Bootstrap::$CONFIG['server'])) {
        if (true == Bootstrap::$CONFIG['server']['media_files']) {
            define('__FILES_DIR__', __HTTP_ROOT__.Bootstrap::$CONFIG['server']['media_files']);
        }
    }
}
unset($setting);

[$__filename] = explode('?', $_SERVER['REQUEST_URI']);
$__request_name = basename($__filename, '.php');
$__script_name = basename($_SERVER['SCRIPT_NAME'], '.php');

if (array_key_exists('job_id', $_REQUEST)) {
    $job_id = $_REQUEST['job_id'];

    $media = Media::get('job_id'.$job_id, 5, function () use ($job_id) {
        $job = Media::$connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);

        // $job = Media_job::where("job_id",$job_id)->getOne();
        // return $job;
        return new Media($job);
    });
    Media::$Obj = $media;
} else {
    Media::$Obj = new Media();
}

if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
    apache_setenv('dont-vary', '1');
}
