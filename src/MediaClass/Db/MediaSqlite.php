<?php
namespace CWP\Db;
use CWP\Db\MediaDb;
/**
 * CWP Media tool
 */

/**
 * CWP Media tool.
 */
class MediaSqlite extends MediaDb
{
    public function checkTable($table)
    {
        return "SELECT name FROM sqlite_master WHERE type='table' AND name='".$table."'";
    }

    public function checkColumn($table, $column)
    {
        return "SELECT 1 FROM pragma_table_info('".$table."') where name='".$column."'";
    }

    public function renameColumn($table, $old, $new)
    {
        return 'ALTER TABLE '.$table." RENAME COLUMN '".$old."'  TO '".$new."';";
    }

    public function createColumn($table, $column, $type)
    {
        return 'ALTER TABLE '.$table.' ADD '.$column.' '.$type.';';
    }

    public function resetTable($table_name)
    {
        return 'DELETE FROM '.$table_name.'; UPDATE sqlite_sequence SET seq = 0 WHERE name="'.$table_name.'"';
    }
}
