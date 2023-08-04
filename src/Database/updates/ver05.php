<?php

$new_column = [
    "form_data" => [
        "tip" => "text"
    ]
];
$new_data = [
    "settings" => [
        [
            "definedName" => '__NAVBAR_LINKS__',
            'setting_value' => '{"Home": "/index.php", "Import": "/import.php", "Settings": { "Language": "/settings/language.php", "Local Settings": "/settings/local.php", "Server Settings": "/settings/settings.php" }}',
            'setting_type' => 'array',
            'setting_name' => 'Navbar Links',
            'setting_description' => 'Navbar links. Name => file.php',
        ]
    ]
];


if (__PROJECT_ROOT__ == "D:\development\cwp_app" || $_SERVER['HTTP_HOST'] == 'plexmedia') {
    $update_data = [
        "settings" => [
            "definedName" => [
                "__NAVBAR_LINKS__" => [
                    'setting_value' => '{"Home": "/index.php", "Import": "/import.php", "Settings": { "Language": "/settings/language.php", "Server Config": "/settings/settings.php", "Local Settings": "/settings/local.php", "Server Settings": "/settings/settings.php"  },"Test":"/test/test.php"}',
                ],
            ],
        ],
    ];
}
