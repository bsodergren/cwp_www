<?php 

$table = $explorer->table("settings");
$table->order("setting_type ASC");
$results = $table->fetchAssoc('id');

if ($results) {

    foreach ($results as $k => $u) {

        $setting[$u['setting_cat']][$u['definedName']] =  [ 
            'id' => $u['id'],
            'type' => $u['setting_type'],
            'value' => $u['setting_value'],
            'name' => $u['setting_name'],
            'description' => $u['setting_description'],
            'category' => $u['setting_cat'],
    ];
        if (!defined($u['definedName'])) {
            define($u['definedName'], $u['setting_value']);
        }

    }

    define("__SETTINGS__", $setting);
}
$const = get_defined_constants(true);
unset($setting);


define("__MEDIA_FILES_DIR__", "/Media Load Flags");

if (MediaSettings::isTrue('__USE_LOCAL_XLSX__')) {
    if (
        MediaSettings::isTrue('__USER_XLSX_DIR__')
    ) {
        define("__FILES_DIR__", __USER_XLSX_DIR__);
        FileSystem::createDir(__FILES_DIR__);
    }
}

if (!MediaSettings::isSet('__FILES_DIR__')) {
    define("__FILES_DIR__", __APP_ROOT__ . __MEDIA_FILES_DIR__);
}

define("__PDF_UPLOAD_DIR__", "/pdf");
define("__ZIP_FILE_DIR__", "/zip");
define("__XLSX_DIRECTORY__", "/xlsx");

$template = new Template();


if (key_exists('job_id', $_REQUEST)) {
    $job_id = $_REQUEST['job_id'];
    $job = $connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
    $media = new Media($job);
}



?>