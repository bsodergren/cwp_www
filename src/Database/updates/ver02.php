<?php
/**
 * CWP Media tool for load flags
 */

$rename_column = [
    'media_job' => [
        'zip_file' => 'zip_exists',
        'xlsx_dir' => 'xlsx_exists',
    ],
];

$new_column    = [
    'media_job' => [
        'base_dir' => 'text',
    ],
];
