<?php #skip
$new_table = ['updates'];
$update_data = [];
$new_data = [];
$rename_column = [];
$new_column = [];


$new_column = ["form_data" => ["tip" => "text"]];
$new_data = [
    "settings" => [
        [
            "definedName" => '__NAVBAR_LINKS__',
            'setting_value' => '{"Test":"/test/test.php","Home":"/index.php","Import":"/import.php","Settings":"/settings.php","Update":"/updater/updater.php"}',
            'setting_type' => 'array',
            'setting_name' => 'Navbar Links',
            'setting_description' => 'Navbar links. Name => file.php',
        ]
    ]
];