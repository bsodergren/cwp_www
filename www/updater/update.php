<?php

use CWP\AutoUpdate\AutoUpdate;
use CWP\HTML\HTMLDisplay;
use CWP\Media\Media;
use Symfony\Component\Process\Process;

/**
 * CWP Media tool.
 */
require '../.config.inc.php';

define('TITLE', 'Media Updater');
include_once __LAYOUT_HEADER__;

if (false === Media::$AutoUpdate->checkUpdate()) {
    exit('Could not check for updates! See log file for details.');
}
if (Media::$AutoUpdate->newVersionAvailable()) {
    // Install new update
    echo 'New Version: '.Media::$AutoUpdate->getLatestVersion().'<br>';
    echo 'Installing Updates: <br>';

    // Optional - empty log file
    $f = @fopen(__UPDATE_LOG_FILE__, 'rb+');
    if (false !== $f) {
        ftruncate($f, 0);
        fclose($f);
    }

    // Optional Callback function - on each version update
    function eachUpdateFinishCallback($updatedVersion)
    {
        $installDir = Media::$AutoUpdate->getInstallDir();

        $updatescript = $installDir.'update.sh';
        $contents = '#!/bin/bash';
        $contents = $contents."\n";
        $contents = $contents.'for i in *; do new=${i//\\/\/}; newd=$(dirname "$new"); mkdir -p "$newd"; mv "$i" "$new"; done';

        file_put_contents($updatescript, $contents);
        chmod($updatescript, 0775);
        $ExecProcess = new Process([$updatescript]);
        $ExecProcess->run(function ($type, $buffer): void {
            if (Process::ERR === $type) {
                echo 'ERR > '.$buffer;
            } else {
                echo 'OUT > '.$buffer;
            }
        });
    }

    Media::$AutoUpdate->onEachUpdateFinish('eachUpdateFinishCallback');

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
    Media::$AutoUpdate->setOnAllUpdateFinishCallbacks('onAllUpdateFinishCallbacks');

    // This call will only simulate an update.
    // Set the first argument (simulate) to "false" to install the update
    // i.e. Media::$AutoUpdate->update(false);
    $result = Media::$AutoUpdate->update(true, false);
    echo '<pre>';
    var_dump(Media::$AutoUpdate->getSimulationResults());
    echo '</pre>';
    if (true === $result) {
        echo 'Update simulation successful<br>';
    } else {
        echo 'Update simulation failed: '.$result.'!<br>';

        if ($result = AutoUpdate::ERROR_SIMULATE) {
            echo '<pre>';
            var_dump(Media::$AutoUpdate->getSimulationResults());
            echo '</pre>';
        }
    }
} else {
    echo 'Current Version is up to date<br>';
}
echo 'All up to date';
// echo HTMLDisplay::JavaRefresh('/index.php', 3);
include_once __LAYOUT_FOOTER__;

// echo 'Log:<br>';
// echo nl2br(file_get_contents(__UPDATE_LOG_FILE__));

exit;

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
    // echo 'All up to date';
    echo HTMLDisplay::JavaRefresh('/index.php', 3);
}

?>