<?php
$new_table = [];
$update_data = [];
$new_data = [];
$rename_column = [];
$new_column = [];


$new_column = ["settings" => ["setting_cat" => "TEXT DEFAULT 'server'"]];


$update_data = [
    "settings" => [
        "definedName" => [
            "__USER_XLSX_DIR__" => ['setting_cat'=>'user'],
            "__USE_LOCAL_XLSX__" => ['setting_cat'=>'user'],
        ],
    ],            
];


