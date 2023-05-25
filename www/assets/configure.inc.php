<?php

use Nette\Utils\FileSystem;


$refresh = MediaUpdate::createDatabase();

$connection = new Nette\Database\Connection(__DATABASE_DSN__);
$storage = new Nette\Caching\Storages\DevNullStorage();
$structure = new Nette\Database\Structure($connection, $storage);
$conventions = new Nette\Database\Conventions\DiscoveredConventions($structure);
$explorer = new Nette\Database\Explorer($connection, $structure, $conventions, $storage);

$UpdaterObj = new MediaUpdate($connection);

if ($UpdaterObj->check_tableExists('updates')) {
    $skip_file_array = [];
    $rows = $connection->fetchAll('SELECT * FROM updates');
    foreach ($rows as $k => $arr) {
        array_push($skip_file_array, $arr['update_filename']);
    }
    $version_updates_skipSkipFile = 0;
}

$updates_array = Utils::get_filelist(__UPDATES_DIR__, 'php', $version_updates_skipSkipFile);

if (count($updates_array) >= 1) {
    $update = new MediaUpdate($connection);
    $update->refresh = $refresh;
    sort($updates_array);
    foreach ($updates_array as $k => $file) {
        $filename = basename($file);
        if (!in_array($filename, $skip_file_array)) {
            $update->versionUpdate($file);
   
        }
    }

    $refresh = $update->refresh;
}

unset($file);
unset($file_array);
unset($include_array);
unset($update);

if ($refresh == true) 
{
    header("Location:  ".__URL_PATH__ . "/index.php");
    exit();
}
