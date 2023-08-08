<?php
namespace CWP\Db;

interface MediaDbAbstract
{

    function query($query);
    function fetch($query);
    function fetchOne($query);

     function check_tableExists($table);

     function check_columnExists($table, $column);

     function rename_column($table, $old, $new);

     function create_column($table, $column, $type);

     function change_column($table_name, $name, $type);

     function reset_table($table);

}