<?php
/**
 * CWP Media tool
 */

namespace CWP\Db;

/**
 * CWP Media tool.
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

    public function getOrigSQL($table_name)
    {
        return 'SELECT sql FROM "main".sqlite_master WHERE tbl_name = "'.$table_name.'" and type = "table"';
    }

    public function createTempTable($tableSQL, $field, $type)
    {
        $tableSQL = preg_replace('/(.*")([a-zA-Z_]+)("\s+\()(.*)/', '$1'.$this->tableTmpName.'$3', $tableSQL);
        $tableSQL = preg_replace('/(\s+"'.$field.'"\s+)([A-Za-z]+)(.*),/m', '$1 '.$type.' $3,', $tableSQL);

        return $tableSQL;
    }

    public function copyTable($OrigTable, $fields = [])
    {
        $field_list = implode('","', $fields);
        $sql = 'INSERT INTO "main"."'.$this->tableTmpName.'"'.\PHP_EOL.' (';
        $sql .= '"'.$field_list.'"';
        $sql .= ') '.\PHP_EOL.'SELECT '.\PHP_EOL;
        $sql .= '"'.$field_list.'"'.\PHP_EOL;
        $sql .= 'FROM "main"."'.$OrigTable.'"';

        return $sql;
    }

    public function getColumns($tableSQL)
    {
        preg_match_all('/"([a-zA-Z_]+)"\s+([A-Za-z]+).*,/m', $tableSQL, $output_array);

        return $output_array[1];
    }

    public function dropTable($table_name)
    {
        return 'DROP TABLE "main"."'.$table_name.'"';
    }

    public function renameTmpTable($table_name)
    {
        return 'ALTER TABLE "main"."'.$this->tableTmpName.'" RENAME TO "'.$table_name.'"';
    }

    public function updateStructure($table_name,$name,$type)
    {
        $sql = $this->getOrigSQL($table_name);
        $tableSQL = $this->conn->queryOne($sql);
        $fields = $this->getColumns($tableSQL);

        $sqlArr[] = $this->createTempTable($tableSQL, $name, $type);
        $sqlArr[] = $this->copyTable($table_name, $fields);
        $sqlArr[] = $this->dropTable($table_name);
        $sqlArr[] = $this->renameTmpTable($table_name);

        foreach ($sqlArr as $sql) {
            $this->conn->queryOne($sql);
            //    echo '<pre>'.$sql.'</pre></br>';
        }
    }
    public function resetTable($table_name)
    {
        return 'DELETE FROM '.$table_name.'; UPDATE sqlite_sequence SET seq = 0 WHERE name="'.$table_name.'"';
    }
}
