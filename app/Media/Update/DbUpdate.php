<?php
/**
 * CWP Media tool
 */

namespace CWP\Media\Update;

/*
 * CWP Media tool
 */

use CWP\Utils;
use CWP\Bootstrap;
use CWP\Db\MediaMySQL;
use CWP\Db\MediaSqlite;
use Nette\Database\Helpers;
use Nette\Utils\FileSystem;
use Nette\Database\Connection;
use CWP\Media\Update\MediaUpdate;

class DbUpdate extends MediaUpdate
{
    public $table_name;

    public $refresh    = false;

    public function versionUpdate($file)
    {
        $new_table     = [];
        $update_data   = [];
        $new_data      = [];
        $rename_column = [];
        $new_column    = [];
        $reset_table   = [];
        $delete_data   = [];

        include_once $file;

        $updates       = [
            'resetTable'    => $reset_table,
            'newTable'      => $new_table,
            'updateColumns' => $rename_column,
            'newColumn'     => $new_column,
            'newData'       => $new_data,
            'updateData'    => $update_data,
            'deleteData'    => $delete_data,
        ];

        foreach ($updates as $classmethod => $data_array) {
            $this->$classmethod($data_array);
        }

        $filename      = basename($file);

        if ($this->check_tableExists('updates')) {
            $this->newData(['updates' => ['update_filename' => $filename]]);
        } else {
            $this->setSkipFile($file);
        }

    }

    public function newData($new_data)
    {
        if (is_array($new_data)) {
            foreach ($new_data as $table => $new_data_vals) {
                $u             = $this->conn->query('INSERT INTO '.$table.' ?', $new_data_vals);
                $this->refresh = true;
            }
        }
    }

    public function query($query)
    {
        try {
            $result = $this->conn->fetch($query);

            return $result;
        } catch (\PDOException   $e) {
            echo 'Caught exception: ',  $e->getMessage(),  $e->getCode() , "\n";
        }
    }

    public function queryOne($query)
    {
        try {
            $result = $this->conn->fetchField($query);

            return $result;
        } catch (\PDOException   $e) {
            echo 'Caught exception: ',  $e->getMessage(),  $e->getCode() , "\n";
        }
    }

    public function check_tableExists($table_name = '')
    {
        return $this->dbClassObj->check_tableExists($table_name);
    }

    public function newTable($new_table)
    {
        if (is_array($new_table)) {
            foreach ($new_table as $table_name) {
                $this->set($table_name);
                if (!$this->check_tableExists($table_name)) {
                    $this->create_table($table_name);
                    $this->refresh = true;
                }
            }
        }
    }

    public function set($table_name)
    {
        $this->table_name = $table_name;
    }

    public function create_column($table, $field, $type)
    {
        $this->dbClassObj->create_column($table, $field, $type);
    }

    public function create_table($table_name)
    {
        $sql_file = FileSystem::normalizePath(__DEFAULT_TABLES_DIR__.'/cwp_table_'.$table_name.'.sql');
        if (file_exists($sql_file)) {
            Helpers::loadFromFile($this->conn, $sql_file);
        }
    }

    public function updateColumns($rename_column)
    {
        if (is_array($rename_column)) {
            foreach ($rename_column as $table_name => $column) {
                $this->set($table_name);
                foreach ($column as $old => $new) {
                    if ($this->dbClassObj->check_columnExists($table_name, $old)) {
                        if (!$this->dbClassObj->check_columnExists($table_name, $new)) {
                            $this->dbClassObj->rename_column($table_name, $old, $new);
                            $this->refresh = true;
                        }
                    }
                }
            }
        }
    }

    public function newColumn($new_column)
    {
        if (is_array($new_column)) {
            foreach ($new_column as $table_name => $column) {
                $this->set($table_name);
                foreach ($column as $field => $type) {
                    if (!$this->dbClassObj->check_columnExists($table_name, $field)) {
                        $this->dbClassObj->create_column($table_name, $field, $type);
                        $this->refresh = true;
                    }
                }
            }
        }
    }

    public function resettable($reset_table)
    {
        if (is_array($reset_table)) {
            foreach ($reset_table as $table_name) {
                $this->set($table_name);
                if ($this->dbClassObj->check_tableExists($table_name)) {
                    $this->dbClassObj->reset_table($table_name);
                    $this->refresh = true;
                }
            }
        }
    }

    public function updateData($update_data)
    {
        if (is_array($update_data)) {
            foreach ($update_data as $table => $updates) {
                foreach ($updates as $where => $data) {
                    foreach ($data as $key => $update_array) {
                        $query         = 'UPDATE '.$table.' ';
                        $query         = $query.'SET ';
                        foreach ($update_array as $field => $value) {
                            $field_array[] = $field." = '".$value."'";
                        }

                        $query .= implode(',', $field_array);
                        unset($field_array);
                        $query .= ' WHERE '.$where." = '".$key."'";
                        $result        = $this->conn->query($query);
                        $this->refresh = true;
                    }
                }
            }
        }
    }

    public function deleteData($delete_data)
    {
        if (is_array($delete_data)) {
            foreach ($delete_data as $table => $updates) {
                foreach ($updates as $data => $val) {
                    $queryArr      = [];

                    if (is_array($val)) {
                        if (!is_int($data)) {
                            $where = $data;
                        } else {
                            $where = $val[0];
                        }
                        $data = $val;
                    }

                    $pre_query     = 'DELETE FROM '.$table.' WHERE ';
                    foreach ($data as $field => $value) {
                        if (!is_int($field)) {
                            if (!isset($where)) {
                                $where = $field;
                            }
                            if ($field != $where) {
                                $where = $field;
                            }
                            $queryArr[] = $where." = '".$value."' ";
                        } else {
                            $query .= $pre_query.$where." = '".$value."'; ";
                        }
                    }
                    unset($where);
                    if (count($queryArr) > 0) {
                        $query = $pre_query.implode(' AND ', $queryArr);
                    }
                    $queryArr      = [];
                    $queries       = explode(';', $query);
                    foreach ($queries as $q) {
                        if (str_contains($q, 'DELETE')) {
                            $result = $this->conn->query($q);
                        }
                    }
                    $this->refresh = true;
                }
            }
        }
    }

    public function checkDbUpdates($refresh)
    {
        if ($this->check_tableExists('updates')) {
            $skip_file_array              = [];
            $rows                         = $this->conn->fetchAll('SELECT * FROM updates');
            foreach ($rows as $k => $arr) {
                $skip_file_array[] = $arr['update_filename'];
            }
            $version_updates_skipSkipFile = 0;
        }
        $updates_array = Utils::get_filelist(__SQL_UPDATES_DIR__, 'php', $version_updates_skipSkipFile);
        sort($updates_array);
        if (count($updates_array) >= 1) {
            $this->refresh   = $refresh;
            sort($updates_array);
            foreach ($updates_array as $k => $file) {
                $filename = basename($file);
                if (!in_array($filename, $skip_file_array)) {
                    $this->versionUpdate($file);
                }
            }

            $refresh         = $this->refresh;
        }
        return $refresh;
    }
}