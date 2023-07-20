<?php
/**
 * CWP Media tool
 */

require '../.config.inc.php';

function get_content($URL)
{
    $ch   = curl_init();
    curl_setopt($ch, \CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, \CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, \CURLOPT_URL, $URL);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

use Symfony\Component\Process\Process;

$gitRaw           = 'https://raw.githubusercontent.com/bsodergren/cwp_www/main/www/updater/';
$updateUrl        = $gitRaw.'version.txt';
$zip_url          = $gitRaw.'Latest.zip';

$builder_exec     = __ROOT_BIN_DIR__.\DIRECTORY_SEPARATOR.'builder.exe';
$patcher_exec     = __ROOT_BIN_DIR__.\DIRECTORY_SEPARATOR.'patcher.exe';

define('TITLE', 'Media Updater');
include_once __LAYOUT_HEADER__;

$version          = get_content($updateUrl);
$current          = file_get_contents(__VERSION_FILE__);

dump([$updateUrl, $version, $current]);

if ($version > $current) {
    echo 'time to update date '.$patcher_exec;

    $data             = get_content($zip_url);

    if (!is_dir(__VERSION_DL_DIR__)) {
        mkdir(__VERSION_DL_DIR__, 0777, true);
    }

    $destination      = __VERSION_DL_DIR__.\DIRECTORY_SEPARATOR.'Latest.zip';

    if (file_exists($destination)) {
        unlink($destination);
    }
    $file             = fopen($destination, 'w+');
    fwrite($file, $data);
    fclose($file);

    $command          = [
        $patcher_exec,
        '-O',
        __DRIVE_LETTER__.$conf['server']['root_dir'],
        '-P',
        $destination,
    ];

    $process          = new Process($command);
    $process->setTimeout(60000);

    $runCommand       = $process->getCommandLine();

    dd($runCommand);

    //            $process->start();
    //            $process->wait($callback);
}

include_once __LAYOUT_FOOTER__;
