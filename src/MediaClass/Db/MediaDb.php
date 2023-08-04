<?php
namespace CWP\Db;

use CWP\Media\Media;
/**
 * CWP Media tool
 */

class MediaDb
{

    public $conn;

    public function __construct($parent, $conn)
    {
        $this->conn = $parent;
    }

    public function check_tableExists($table = '')
    {
        $query = $this->checkTable($table);

        return $this->conn->queryOne($query);
    }

    public function check_columnExists($table, $column)
    {
        $query = $this->checkColumn($table, $column);

        return $this->conn->queryOne($query);
    }

    public function rename_column($table, $old, $new)
    {
        $query = $this->renameColumn($table, $old, $new);

        return $this->conn->queryOne($query);
    }

    public function create_column($table, $column, $type)
    {
        $query = $this->createColumn($table, $column, $type);

        return $this->conn->queryOne($query);
    }

    public function reset_table($table)
    {
        $query = $this->resetTable($table);

        return $this->conn->queryOne($query);
    }
}
