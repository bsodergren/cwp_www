<?php
/**
 * CWP Media Load Flag Creator
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
        // ['TEXT' => 'VARCHAR(255)'],
    ];

    public function query($query)
    {
        try {
            return Media::$connection->query($query);
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

            foreach ($result as $row) {
                return $row->cnt;
            }
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
        // $query = 'SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = "'.$table.'" AND COLUMN_NAME = "'.$column.'";';
        $query = 'SHOW COLUMNS FROM `'.$table.'`  LIKE "'.$column.'";';
        utmdump($query);
        $res = $this->query($query);

        return $res->getRowCount();
    }

    public function check_tableExists($table)
    {
        $query = "SELECT count(*) as cnt FROM information_schema.tables WHERE table_schema = '".DB_DATABASE."' AND table_name = '".$table."'";

        return $this->queryExists($query);
    }

    public function rename_column($table, $old, $new)
    {
        $query = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '".$table."' AND COLUMN_NAME = '".$old."';";
        $result = $this->fetchOne($query);
        $query = 'ALTER TABLE `'.$table.'` CHANGE `'.$old.'` `'.$new.'` '.$result.';';

        return $this->query($query);
    }

    public function change_column($table_name, $name, $type)
    {
        $type = $this->sanitizeFields($type);
        $query = 'ALTER TABLE `'.$table_name.'` CHANGE `'.$name.'` `'.$name.'` '.$type.';';
        $result = $this->query($query);
    }

    public function drop_column($table_name, $name)
    {
        $query = 'ALTER TABLE `'.$table_name.'` DROP `'.$name.'`;';
        $result = $this->query($query);
    }

    public function create_column($table, $column, $type)
    {
        $column_type = $this->sanitizeFields($type);
        if (\is_array($type)) {
            foreach ($type as $key => $value) {
                if ('INT' == $key || 'TEXT' == $key) {
                    $column_type = $key.'('.$value.') ';
                    continue;
                }
                if ('DEFAULT' == $key) {
                    $column_type = $column_type.$key.' '.$value;
                }
            }
        }

        $query = 'ALTER TABLE '.$table.' ADD `'.$column.'` '.$column_type.';';
        $result = $this->query($query);
    }

    public function reset_Table($table_name)
    {
        $query = 'TRUNCATE `'.$table_name.'`; ';
        $result = $this->query($query);
    }

    public function tableAlterADD($table, $action, $column)
    {
        $query = 'ALTER TABLE '.$table.' ADD '.strtoupper($action).'(`'.$column.'`);';
        $result = $this->query($query);
        // ALTER TABLE `form_data` ADD UNIQUE(`original`);
        //
    }
}
