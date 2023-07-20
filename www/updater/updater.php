<?php
/**
 * CWP Media tool
 */

require '../.config.inc.php';

define('__VERSION_DL_DIR__', __DRIVE_LETTER__.$conf['server']['root_dir'].$conf['server']['web_root'].\DIRECTORY_SEPARATOR.'updater/download');
define('__VERSION_FILE__', __DRIVE_LETTER__.$conf['server']['root_dir'].$conf['server']['web_root'].\DIRECTORY_SEPARATOR.'updater/current.txt');

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

$gitRaw             = 'https://raw.githubusercontent.com/bsodergren/cwp_www/main/www/updater/';
$updateUrl          = $gitRaw.'current.txt?432=432';
$versionUrl         = $gitRaw.'version.txt?432=432';
$zip_url            = $gitRaw.'versions/';

$builder_exec       = __ROOT_BIN_DIR__.\DIRECTORY_SEPARATOR.'builder.exe';
$patcher_exec       = __ROOT_BIN_DIR__.\DIRECTORY_SEPARATOR.'patcher.exe';

define('TITLE', 'Media Updater');
include_once __LAYOUT_HEADER__;

$current            = trim(get_content($updateUrl));
$installed          = trim(file_get_contents(__VERSION_FILE__));

if ($current > $installed) {
    echo 'time to update date <br>';

    $allVersions         = get_content($versionUrl);
    $verArray            = explode("\n", $allVersions);
    foreach ($verArray as $Updates) {
        if ($installed >= trim($Updates)) {
            continue;
        }
        $doUpdates[]    = trim($Updates);
    }

    echo 'There are '.count($doUpdates).' updates pending<br>';
    if (array_key_exists('update', $_POST)) {
        foreach ($doUpdates as $version) {
            $zip_filename           = 'update_'.$version.'.zip';
            $zip_dl_url             = $zip_url.$zip_filename;
            $data                   = get_content($zip_dl_url);

            if (!is_dir(__VERSION_DL_DIR__)) {
                mkdir(__VERSION_DL_DIR__, 0777, true);
            }

            $destination            = __VERSION_DL_DIR__.\DIRECTORY_SEPARATOR.$zip_filename;
            $updateFiles[]          = $destination;
            if (file_exists($destination)) {
                unlink($destination);
            }
            $file                   = fopen($destination, 'w+');
            fwrite($file, $data);
            fclose($file);
        }

        foreach ($updateFiles as $updateFile) {
            echo 'Running update on '.basename($updateFile).'<br>';

            $command             = [
                $patcher_exec,
                '-O',
                __DRIVE_LETTER__.$conf['server']['root_dir'],
                '-P',
                $updateFile,
            ];

            $process             = new Process($command);
            $process->setTimeout(60000);

            $runCommand          = $process->getCommandLine();

            $process->run(function ($type, $buffer): void {
                if (Process::ERR === $type) {
                    echo 'ERR > '.$buffer.'<br>';
                } else {
                    echo 'OUT > '.$buffer.'<br>';
                }
            });
            // $process->wait();
        }
    }
} else {
    echo 'All uo to date';
}

include_once __LAYOUT_FOOTER__;
