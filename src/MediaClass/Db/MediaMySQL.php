<?php
/**
 * CWP Media tool
 */

namespace CWP\Db;

use CWP\Media\Media;

/**
 * CWP Media tool.
 */

/**
 * CWP Media tool.
 */
class MediaMySQL extends MediaDb
{
    private $fieldTranslate = [
        ['TEXT' => 'VARCHAR(255)'],
    ];

    private function sanitizeFields($type)
    {
        foreach ($this->fieldTranslate as $x => $fields) {
            foreach ($fields as $first => $replace) {
                $type = str_ireplace($first, $replace, $type);
            }
        }

        return $type;
    }

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
        $query = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$table."' AND COLUMN_NAME = '".$old."';";
        $result = Media::$connection->queryOne($query);
        $query = 'ALTER TABLE `'.$table.'` CHANGE `'.$old.'` `'.$new.'` '.$result.';';

        return $query;
    }

    public function updateStructure($table_name, $name, $type)
    {
        $type = $this->sanitizeFields($type);
        $query = 'ALTER TABLE `'.$table_name.'` CHANGE `'.$name.'` `'.$name.'` '.$type.';';
        $result = Media::$connection->queryOne($query);
    }

    public function createColumn($table, $column, $type)
    {
        $type = $this->sanitizeFields($type);

        return 'ALTER TABLE '.$table.' ADD '.$column.' '.$type.';';
    }

    public function resetTable($table_name)
    {
        return 'TRUNCATE `'.$table_name.'`; ';
    }
}
