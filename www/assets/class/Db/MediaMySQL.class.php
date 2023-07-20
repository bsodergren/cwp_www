<?php
/**
 * CWP Media tool
 */

/**
 * CWP Media tool.
 */
class MediaMySQL extends MediaDb
{
    public function checkColumn($table, $column)
    {
        return 'SHOW COLUMNS FROM '.$table." LIKE '%".$column."%'";
    }

    public function checkTable($table)
    {
        return "SELECT count(*) FROM information_schema.tables WHERE table_schema = '".DB_DATABASE."' AND table_name = '".$table."'";
    }

    public function renameColumn($table, $old, $new)
    {
        $query  = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$table."' AND COLUMN_NAME = '".$old."';";
        $result = $this->conn->queryOne($query);

        return 'ALTER TABLE `'.$table.'` CHANGE `'.$old.'` `'.$new.'` '.$result.';';
    }

    public function createColumn($table, $column, $type)
    {
        $type = str_ireplace('TEXT', 'VARCHAR(255)', $type);

        return 'ALTER TABLE '.$table.' ADD '.$column.' '.$type.';';
    }

    public function resetTable($table_name)
    {
        return 'TRUNCATE `'.$table_name.'`; ';
    }
}
