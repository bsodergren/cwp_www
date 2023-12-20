<?php

namespace CWP\Database;

use CWP\Database\MysqliDb;

class Database extends MysqliDb
{

    public function __construct()
    {
        parent::__construct(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

    }

    public function getQuery($tableName, $numRows = null, $columns = '*')
    {
        if (empty($columns)) {
            $columns = '*';
        }

        $column       = is_array($columns) ? implode(', ', $columns) : $columns;

        if (!str_contains($tableName, '.')) {
            $this->_tableName = self::$prefix.$tableName;
        } else {
            $this->_tableName = $tableName;
        }

        $this->_query = 'SELECT '.implode(' ', $this->_queryOptions).' '.
            $column.' FROM '.$this->_tableName;

        $stmt         = $this->_buildQuery($numRows);

        if ($this->isSubQuery) {
            return $this;
        }

        return $this->_lastQuery;
    }
}
