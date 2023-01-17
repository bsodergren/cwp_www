<?php

use Nette\Utils\FileSystem;

$refresh = false;
if (!file_exists(__SQLITE_DATABASE__)) {
   $connection = new Nette\Database\Connection(__DATABASE_DSN__);
    $_default_sql_dir = FileSystem::normalizePath(__SQLITE_DIR__ . "/default");
    $file_tableArray = MediaUpdate::get_filelist($_default_sql_dir, 'cwp_table.*)\.(sql', 0);


    foreach ($file_tableArray as $k => $sql_file) {
        $table_name = str_replace("cwp_table_", "", basename($sql_file, ".sql"));
        $connection->query("drop table if exists " . $table_name);
        Nette\Database\Helpers::loadFromFile($connection, $sql_file);
    }

    unset($sql_file);
    Nette\Database\Helpers::loadFromFile($connection, $_default_sql_dir . '/cwp_data.sql');
    unset($_default_sql_dir);

    $refresh = true;
    $version_updates_skipSkipFile = 0;
} else {

    $connection = new Nette\Database\Connection(__DATABASE_DSN__);
    $storage = new Nette\Caching\Storages\DevNullStorage();
    $structure = new Nette\Database\Structure($connection, $storage);
    $conventions = new Nette\Database\Conventions\DiscoveredConventions($structure);
    $explorer = new Nette\Database\Explorer($connection, $structure, $conventions, $storage);


    $version_updates_skipSkipFile = 1;
}

$tmpU = new MediaUpdate($connection);

if ($tmpU->check_tableExists('updates')) {
    $skip_file_array = [];
    $rows = $connection->fetchAll('SELECT * FROM updates');
    foreach ($rows as $k => $arr) {
        array_push($skip_file_array, $arr['update_filename']);
    }
    $version_updates_skipSkipFile = 0;
}

$updates_array = MediaUpdate::get_filelist(__UPDATES_DIR__, 'php', $version_updates_skipSkipFile);
if (count($updates_array) >= 1) {

    $update = new MediaUpdate($connection);
    $update->refresh = $refresh;

    foreach ($updates_array as $k => $file) {
        $filename = basename($file);
        if (!in_array($filename, $skip_file_array)) {
            $update->versionUpdate($file);
        }
    }

    $refresh = $update->refresh;
}

if ($refresh == true) {
    HTMLDisplay::javaRefresh("index.php", 0);
    ob_flush();
}
