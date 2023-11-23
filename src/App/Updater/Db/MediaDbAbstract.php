<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Updater\Db;

interface MediaDbAbstract
{
    public function query($query);

    public function fetch($query);

    public function fetchOne($query);

    public function check_tableExists($table);

    public function check_columnExists($table, $column);

    public function rename_column($table, $old, $new);

    public function create_column($table, $column, $type);

    public function change_column($table_name, $name, $type);

    public function reset_table($table);
    public function tableAlterADD($table, $action, $column);
}
