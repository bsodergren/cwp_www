<?php

$new_data = [
    "settings" => [
        [
            "definedName" => '__PAGES_PER_XLSX__',
            'setting_value' => '3',
            'setting_type' => 'text',
            'setting_name' => "Print Pages",
            'setting_description' => "Pages per excep worksheet",
            'setting_cat'=>'local',
        ],
    ]
];

$delete_data = [
    "settings" => [
        "definedName" => ['__USER_XLSX_DIR__','__USE_LOCAL_XLSX__','__SHOW_DEBUG_PANEL__'],
    ],
];
