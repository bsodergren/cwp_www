<?php
/**
 * CWP Media tool for load flags
 */

$new_column = [
    'settings' => [
        'setting_cat' => 'TEXT NOT NULL',
    ],
];

$update_data = [
    'settings' => [
        'definedName' => [
            '__USER_XLSX_DIR__' => ['setting_cat' => 'local'],
            '__USE_LOCAL_XLSX__' => ['setting_cat' => 'local'],
        ],
    ],
];
