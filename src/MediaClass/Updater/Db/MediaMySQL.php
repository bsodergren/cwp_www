<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Updater\Db;

use CWP\Core\Media;

/**
 * CWP Media tool.
 */

/**
 * CWP Media tool.
 */
class MediaMySQL extends MediaDb implements MediaDbAbstract
{
    private $fieldTranslate = [
        ['TEXT' => 'VARCHAR(255)'],
    ];

    public function query($query)
    {
        try {
            $result = Media::$connection->query($query);

            return $result;
        } catch (\PDOException   $e) {
            echo 'Caught exception: ',  $e->getMessage(),  $e->getCode() , "\n";
        }
    }

    public function fetch($query)
    {
        try {
            return Media::$connection->fetch($query);
        } catch (\PDOException   $e) {
            echo 'Caught exception: ',  $e->getMessage(),  $e->getCode() , "\n";
        }
    }

    public function fetchOne($query)
    {
        try {
            return Media::$connection->fetchField($query);
        } catch (\PDOException   $e) {
            echo 'Caught exception: ',  $e->getMessage(),  $e->getCode() , "\n";
        }
    }

    public function queryExists($query)
    {
        try {
            $result = Media::$connection->query($query);

            return $result->getRowCount();
        } catch (\PDOException   $e) {
            echo 'Caught exception: ',  $e->getMessage(),  $e->getCode() , "\n";
        }
    }

    private function sanitizeFields($type)
    {
        foreach ($this->fieldTranslate as $x => $fields) {
            foreach ($fields as $first => $replace) {
                $type = str_ireplace($first, $replace, $type);
            }
        }

        return $type;
    }

    public function check_columnExists($table, $column)
    {
        $query = 'SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "'.$table.'" AND COLUMN_NAME = "'.$column.'";';

        return $this->queryExists($query);
    }

    public function check_tableExists($table)
    {
        $query = "SELECT count(*) FROM information_schema.tables WHERE table_schema = '".DB_DATABASE."' AND table_name = '".$table."'";

        return $this->queryExists($query);
    }

    public function rename_column($table, $old, $new)
    {
        $query = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$table."' AND COLUMN_NAME = '".$old."';";
        $result = $this->fetchOne($query);
        $query = 'ALTER TABLE `'.$table.'` CHANGE `'.$old.'` `'.$new.'` '.$result.';';
        $result = $this->query($query);

        return $result;
    }

    public function change_column($table_name, $name, $type)
    {
        $type = $this->sanitizeFields($type);
        $query = 'ALTER TABLE `'.$table_name.'` CHANGE `'.$name.'` `'.$name.'` '.$type.';';
        $result = $this->query($query);
    }

    public function create_column($table, $column, $type)
    {
        $type = $this->sanitizeFields($type);
        $query = 'ALTER TABLE '.$table.' ADD `'.$column.'` '.$type.';';

        $result = $this->query($query);
    }

    public function reset_Table($table_name)
    {
        $query = 'TRUNCATE `'.$table_name.'`; ';
        $result = $this->query($query);
    }
}
