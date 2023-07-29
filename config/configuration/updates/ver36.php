<?php
/**
 * CWP Media tool
 */

$delete_data = [
    'settings' => [
        'definedName' => ['__HALF_FORM_CNT__'],
    ],
];

$new_column = [
    'settings' => [
        'require' => 'TEXT',
    ],
];

$update_data = [
    'settings' => [
        'definedName' => [
            '__SHOW_MAIL__' => ['require' => '__IMAP_ENABLE__'],
            '__IMAP_ENABLE__' => ['require' => '__IMAP_HOST__'],
        ],
    ],
];
