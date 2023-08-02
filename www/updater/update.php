<?php

use CWP\AutoUpdate\AutoUpdate;
use CWP\HTML\HTMLDisplay;
use CWP\Media\Update\AppUpdate;
use Monolog\Logger;
use Nette\Utils\FileSystem;

/**
 * CWP Media tool.
 */
require '../.config.inc.php';

$url = 'https://raw.githubusercontent.com/bsodergren/cwp_www/main/AppUpdates';

$downloadTmpDir = FileSystem::normalizePath(__PUBLIC_ROOT__.'/temp');
$cachempDir = FileSystem::normalizePath(__PUBLIC_ROOT__.'/cache');

$update = new AutoUpdate($downloadTmpDir, __PUBLIC_ROOT__, 60);
$update->setCurrentVersion('1.3.5');
$update->setUpdateUrl($url);

$logger = new Logger('default');
$logger->pushHandler(new Monolog\Handler\StreamHandler(__DIR__.'/update.log'));
$update->setLogger($logger);

// Cache (optional but recommended)
$cache = new \CWP\Cache\File($cachempDir);
$update->setCache($cache, 3600);

if (false === $update->checkUpdate()) {
    exit('Could not check for updates! See log file for details.');
}

if ($update->newVersionAvailable()) {
    // Install new update
    echo 'New Version: '.$update->getLatestVersion().'<br>';
    echo 'Installing Updates: <br>';
    echo '<pre>';
    var_dump(array_map(function ($version) {
        return (string) $version;
    }, $update->getVersionsToUpdate()));
    echo '</pre>';

    // Optional - empty log file
    $f = @fopen(__DIR__.'/update.log', 'rb+');
    if (false !== $f) {
        ftruncate($f, 0);
        fclose($f);
    }

    // Optional Callback function - on each version update
    function eachUpdateFinishCallback($updatedVersion)
    {

        echo '<h3>CALLBACK for version '.$updatedVersion.'</h3>';
    }


    $update->onEachUpdateFinish('eachUpdateFinishCallback');

    // Optional Callback function - on each version update
    function onAllUpdateFinishCallbacks($updatedVersions)
    {
        echo '<h3>CALLBACK for all updated versions:</h3>';
        echo '<ul>';
        foreach ($updatedVersions as $v) {
            echo '<li>'.$v.'</li>';
        }
        echo '</ul>';
    }
    $update->setOnAllUpdateFinishCallbacks('onAllUpdateFinishCallbacks');

    // This call will only simulate an update.
    // Set the first argument (simulate) to "false" to install the update
    // i.e. $update->update(false);
    $result = $update->update();

    if (true === $result) {
        echo 'Update simulation successful<br>';
    } else {
        echo 'Update simulation failed: '.$result.'!<br>';

        if ($result = AutoUpdate::ERROR_SIMULATE) {
            echo '<pre>';
            var_dump($update->getSimulationResults());
            echo '</pre>';
        }
    }
} else {
    echo 'Current Version is up to date<br>';
}

echo 'Log:<br>';
echo nl2br(file_get_contents(__DIR__.'/update.log'));

exit;

define('TITLE', 'Media Updater');
include_once __LAYOUT_HEADER__;

if (false !== AppUpdate::$UPDATES_PENDING) {
    if (key_exists('update', $_POST)) {
        $mediaUpdates->getUpdateFiles();
        $mediaUpdates->doUpdates();

        // echo HTMLDisplay::JavaRefresh('/index.php', 0);
        ob_flush();
    } else {
        echo 'There are '.AppUpdate::$UPDATES_PENDING.' Pending <br>';
        ?>
<form action="/updater/updater.php" method="post">
	<input type="hidden" name="update" value="1">
	<button type="submit" name="submit" class="btn active">Update!</button>
</form>
<?php

    }
} else {
    echo 'All up to date';
    echo HTMLDisplay::JavaRefresh('/index.php', 3);
}

include_once __LAYOUT_FOOTER__;
?>