<?php

$table = $explorer->table('settings');
$table->order('setting_type ASC');
$results = $table->fetchAssoc('id');

if ($results) {
    foreach ($results as $k => $u) {
        $setting[$u['setting_cat']][$u['definedName']] = [
            'id' => $u['id'],
            'type' => $u['setting_type'],
            'value' => $u['setting_value'],
            'name' => $u['setting_name'],
            'description' => $u['setting_description'],
            'category' => $u['setting_cat'],
        ];
        if (! defined($u['definedName'])) {
            define($u['definedName'], $u['setting_value']);
        }
    }

    define('__SETTINGS__', $setting);
}
$const = get_defined_constants(true);

unset($setting);

define('__FILES_DIR__', $conf['server']['root_dir'].$conf['server']['web_root'].$conf['server']['file_root']);

define('__EMAIL_PDF_UPLOAD_DIR__', DIRECTORY_SEPARATOR.'uploads');
define('__PDF_UPLOAD_DIR__', DIRECTORY_SEPARATOR.'pdf');
define('__ZIP_FILE_DIR__', DIRECTORY_SEPARATOR.'zip');
define('__XLSX_DIRECTORY__', DIRECTORY_SEPARATOR.'xlsx');
define('__XLSX_SLIPS_DIRECTORY__', DIRECTORY_SEPARATOR.__XLSX_DIRECTORY__.DIRECTORY_SEPARATOR.'slipsheets');

$template = new Template();

if (array_key_exists('job_id', $_REQUEST)) {
    $job_id = $_REQUEST['job_id'];
    $job = $connection->fetch('SELECT * FROM media_job WHERE job_id = ?', $job_id);
    $media = new Media($job);
}
define('__NAVBAR_LINKS__',
    ['Home' => '/index.php',
        'Import' => '/import.php',
        'Settings' => [
            'Trim Sizes' => '/settings/trim.php',
            'Language' => '/settings/language.php',
            'Local Settings' => '/settings/local.php',
//            'Server Settings' => '/settings/settings.php',
        ],
    ]);
