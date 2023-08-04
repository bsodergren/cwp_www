<?php


$rename_column = [
    "settings" => [
        "setting_name" => "definedName"
    ]
];
$new_column = [
    "settings" => [
        "setting_name" => "text",
        "setting_description" => "text"
    ]
];


$update_data = [
    "settings" => [
        "definedName" => [
            "__USER_XLSX_DIR__" => [
                'setting_name'=>'Media Files',
                'setting_description' => 'User Media Directory'],
            "__USE_LOCAL_XLSX__" => [
                'setting_name'=>'Use Custom Media Dir',
                'setting_description' => 'User Media Directory'],

        ],
    ],
];
