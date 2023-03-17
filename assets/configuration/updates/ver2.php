<?php #skip 
$new_table = [];
$update_data = [];
$new_data = [];
$rename_column = [];
$new_column = [];

$rename_column = ["media_job" => [
    "zip_file" => "zip_exists",
    "xlsx_dir" => "xlsx_exists"
]];

$new_column = ["media_job" => ["base_dir" => "text"]];
