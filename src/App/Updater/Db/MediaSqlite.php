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
class MediaSqlite extends MediaDb implements MediaDbAbstract
{
    public function __call($method, $args)
    {
        $method = strtolower($method);
        switch ($method) {
            case 'insert':
            case 'update':
            case 'drop':
            case 'alter':
            case 'create':
            case 'delete':
                $this->query($args[0]);
                break;
            default:
                dd('Method '.$method.' not found');
                break;
        }
    }

    public function query($query)
    {
        try {
            Media::$connection->beginTransaction();
            $result = Media::$connection->query($query);
            Media::$connection->commit();

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

    public function check_tableExists($table)
    {
        $query = "SELECT name FROM sqlite_master WHERE type='table' AND name='".$table."'";

        return $this->fetchOne($query);
    }

    public function check_columnExists($table, $column)
    {
        $query = "SELECT 1 FROM pragma_table_info('".$table."') where name='".$column."'";

        return $this->fetchOne($query);
    }

    public function rename_Column($table, $old, $new)
    {
        $query = 'ALTER TABLE '.$table." RENAME COLUMN '".$old."'  TO '".$new."';";

        return $this->alter($query);
    }

    public function drop_Column($table, $column)
    {
        $query = 'ALTER TABLE '.$table.' DROP '.$column.';';

        return $this->alter($query);
    }
    public function create_Column($table, $column, $type)
    {
        $query = 'ALTER TABLE '.$table.' ADD '.$column.' '.$type.';';

        return $this->alter($query);
    }
    public function reset_table($table_name)
    {
        $this->delete('DELETE FROM '.$table_name.'; UPDATE sqlite_sequence SET seq = 0 WHERE name="'.$table_name.'"');
    }

    public function change_column($table_name, $name, $type)
    {
        $tableSQL = $this->getOrigSQL($table_name);
        $fields   = $this->getColumns($tableSQL);

        $this->createTempTable($tableSQL, $name, $type);
        $this->copyTable($table_name, $fields);
        $this->dropTable($table_name);
        $this->renameTmpTable($table_name);
    }

    private function cleanTableSQL($tableSQL)
    {
        $sep          = "\n\t";

        $tableSQL     = trim(str_replace("\n", '\x', $tableSQL));
        $tableSQL     = preg_replace("/\s{2,}/", ' ', $tableSQL);
        $tableSQL     = trim(str_replace("\t", ' ', $tableSQL));
        $tableSQL     = trim(str_replace("\x", "\n", $tableSQL));
        $sql_array    = explode("\n", $tableSQL);

        $start_query  = $sql_array[0];
        $sql_array    = array_reverse($sql_array);
        array_pop($sql_array);
        $sql_array    = array_reverse($sql_array);
        $fieldsList   = '';
        $endQuery     = [];

        foreach ($sql_array as $row) {
            if (str_contains($row, ')')) {
                $endQuery[] = $row;
                continue;
            }
            $fieldsList .= trim($row);
        }

        $fieldsList   = str_replace(',', ",\n", $fieldsList);

        $fieldsListAr = explode("\n", $fieldsList);
        array_walk($fieldsListAr, function (&$value, $key) {
            $value = trim($value);
            if (! str_contains($value, '"')) {
                $value = preg_replace("/([a-zA-Z_]+)\s+(.*)/", '"$1" $2', $value);
            }
        });

        $end_str      = implode($sep, $endQuery);
        $fieldsList   = implode($sep, $fieldsListAr);
        $tableSQL     = $start_query.$sep.$fieldsList.$sep.$end_str;

        return $tableSQL;
    }

    private function getOrigSQL($table_name)
    {
        $tableSQL = $this->fetchOne('SELECT sql FROM "main".sqlite_master WHERE tbl_name = "'.$table_name.'" and type = "table"');

        return $this->cleanTableSQL($tableSQL);
    }

    private function createTempTable($tableSQL, $field, $type)
    {
        $tableSQL = preg_replace('/(.*")([a-zA-Z_]+)("\s+\()(.*)/', '$1'.$this->tableTmpName.'$3', $tableSQL);
        $tableSQL = preg_replace('/(\s+"'.$field.'"\s+)([A-Za-z]+)(.*),/m', '$1'.$type.'$3,', $tableSQL);

        if (null !== $this->check_tableExists($this->tableTmpName)) {
            $this->dropTable($this->tableTmpName);
        }
        $this->create($tableSQL);
    }

    private function copyTable($OrigTable, $fields = [])
    {
        $sep        = ' ';
        array_walk($fields, function (&$value, $key) {
            $value = trim($value);
            $value = '"'.$value.'"';
        });
        $field_list = implode(',', $fields);

        $sql[]      = 'INSERT INTO "main"."'.$this->tableTmpName.'"';
        $sql[]      = '('.$field_list.')';
        $sql[]      = 'SELECT';
        $sql[]      = $field_list;
        $sql[]      = 'FROM "main"."'.$OrigTable.'"';
        $query      = implode($sep, $sql);
        $this->insert($query);
    }

    private function getColumns($tableSQL)
    {
        preg_match_all('/"([a-zA-Z_]+)"\s+([A-Za-z]+).*,/m', $tableSQL, $output_array);

        return $output_array[1];
    }

    private function dropTable($table_name)
    {
        $this->drop('DROP TABLE "main"."'.$table_name.'"');
    }

    private function renameTmpTable($table_name)
    {
        $this->alter('ALTER TABLE "main"."'.$this->tableTmpName.'" RENAME TO "'.$table_name.'"');
    }
}
