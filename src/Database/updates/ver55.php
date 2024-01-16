<?php
/**
 * CWP Media tool for load flags.
 */
$new_data = [
'settings' => [
    [
        'definedName' => '__USE_GOOGLE__',
        'setting_value' => '',
        'setting_type' => 'bool',
        'setting_name' => 'Use Google',
        'setting_description' => 'Use Google Drive',
        'setting_cat' => 'local',
        'setting_group' => 'Google',
    ],
    [
        'definedName' => '__GOOGLE_CLIENTID__',
        'setting_value' => '',
        'setting_type' => 'text',
        'setting_name' => 'Google ClientID',
        'setting_description' => 'the client ID',
        'setting_cat' => 'local',
        'setting_group' => 'Google',
    ],
    [
        'definedName' => '__GOOGLE_SECRET__',
        'setting_value' => '',
        'setting_type' => 'text',
        'setting_name' => 'Google Secret',
        'setting_description' => 'the client Secret',
        'setting_cat' => 'local',
        'setting_group' => 'Google',
    ],
    [
        'definedName' => '__GOOGLE_TOKEN__',
        'setting_value' => '',
        'setting_type' => 'text',
        'setting_name' => 'Google Token',
        'setting_description' => 'the client Token',
        'setting_cat' => 'local',
        'setting_group' => 'Google',
    ],
    [
        'definedName' => '__GOOGLE_SHARE_URL__',
        'setting_value' => '',
        'setting_type' => 'text',
        'setting_name' => 'Google Shared URL',
        'setting_description' => 'the google URL',
        'setting_cat' => 'local',
        'setting_group' => 'Google',
    ],
],
];

$update_data = [
   'settings' => [
       'definedName' => [
           '__PAGES_PER_XLSX__' => ['setting_group' => 'Misc'],
           '__SHOW_DECIMAL__' => ['setting_group' => 'Misc'],
           '__IMAP_ENABLE__' => ['setting_group' => 'iMap'],
           '__IMAP_HOST__' => ['setting_group' => 'iMap'],
           '__IMAP_USER__' => ['setting_group' => 'iMap'],
           '__IMAP_PASSWD__' => ['setting_group' => 'iMap'],
           '__IMAP_FOLDER__' => ['setting_group' => 'iMap'],
           '__SHOW_ZIP__' => ['setting_group' => 'Misc'],
           '__SHOW_MAIL__' => ['setting_group' => 'iMap'],
           '__MEDIA_FILES_DIR__' => ['setting_group' => 'Misc'],
           '__DROPBOX_AUTH_TOKEN__' => ['setting_group' => 'Dropbox'],
           '__USE_DROPBOX__' => ['setting_group' => 'Dropbox'],
           '__DROPBOX_APP_KEY__' => ['setting_group' => 'Dropbox'],
           '__DROPBOX_APP_SECRET__' => ['setting_group' => 'Dropbox'],
       ],
   ],
];
