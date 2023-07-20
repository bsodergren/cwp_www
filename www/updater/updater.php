<?php
/**
 * CWP Media tool
 */

require '../.config.inc.php';

use Symfony\Component\Process\Process;

$builder_exec    = __ROOT_BIN_DIR__.\DIRECTORY_SEPARATOR.'builder.exe';
$patcher_exec    = __ROOT_BIN_DIR__.\DIRECTORY_SEPARATOR.'patcher.exe';

define('TITLE', 'Media Updater');
include_once __LAYOUT_HEADER__;

$version         = file_get_contents('https://raw.githubusercontent.com/bsodergren/cwp_www/main/www/updater/version.txt');
$current         = file_get_contents(__VERSION_FILE__);

if ($version > $current) {
    $zip_url        = 'https://github.com/bsodergren/cwp_www/raw/main/www/updater/Latest.zip';
    echo 'time to update date '.$patcher_exec;

    $ch             = curl_init();
    curl_setopt($ch, \CURLOPT_URL, $zip_url);
    curl_setopt($ch, \CURLOPT_RETURNTRANSFER, 1);
    $data           = curl_exec($ch);
    curl_close($ch);
    if (!is_dir(__VERSION_DL_DIR__)) {
        mkdir(__VERSION_DL_DIR__, 0777, true);
    }

    $destination    = __VERSION_DL_DIR__.\DIRECTORY_SEPARATOR.'Latest.zip';

    if (file_exists($destination)) {
        unlink($destination);
    }
    $file           = fopen($destination, 'w+');
    fwrite($file, $data);
    fclose($file);

    $command  = [
        $patcher_exec,
        '-O',
        __DRIVE_LETTER__.$conf['server']['root_dir'],
        '-P',
        $destination,

    ];


    $process          = new Process($command);
    $process->setTimeout(60000);

    $runCommand = $process->getCommandLine();

    dd($runCommand);

//            $process->start();
//            $process->wait($callback);


}

include_once __LAYOUT_FOOTER__;
