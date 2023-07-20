<?php
/**
 * CWP Media tool
 */

use Nette\Utils\FileSystem;

class MediaUpdate
{
    public $table_name;

    public $refresh    = false;

    protected $conn;

    public $dbClassObj = '';

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

    public function __construct($db_conn)
    {
        global $conf;
        $this->conn       = $db_conn;
        $dbType           = 'sqlite';

        if ('mysql' == $conf['db']['type']) {
            $dbType = 'mysql';
        }
        $this->dbClassObj = new $dbType($this, $db_conn);
    }

    public function query($query)
    {
        try {
            $result = $this->conn->fetch($query);

            return $result;
        } catch (PDOException   $e) {
            echo 'Caught exception: ',  $e->getMessage(),  $e->getCode() , "\n";
        }
    }

    public function queryOne($query)
    {
        try {
            $result = $this->conn->fetchField($query);

            return $result;
        } catch (PDOException   $e) {
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
                if (!$this->dbClassObj->check_tableExists($table_name)) {
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
            Nette\Database\Helpers::loadFromFile($this->conn, $sql_file);
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

    public static function createDatabase()
    {
        global $conf;
        if (!file_exists(__SQLITE_DATABASE__)) {
            FileSystem::createDir(__SQLITE_DIR__);
            if ('mysql' == $conf['db']['type']) {
                touch(__SQLITE_DATABASE__);
            }
            $connection       = new Nette\Database\Connection(__DATABASE_DSN__, DB_USERNAME, DB_PASSWORD);
            $_default_sql_dir = FileSystem::normalizePath(__DEFAULT_TABLES_DIR__);
            $file_tableArray  = Utils::get_filelist($_default_sql_dir, 'cwp_table.*)\.(sql', 0);
            foreach ($file_tableArray as $k => $sql_file) {
                $table_name = str_replace('cwp_table_', '', basename($sql_file, '.sql'));
                $connection->query('drop table if exists '.$table_name);
                Nette\Database\Helpers::loadFromFile($connection, $sql_file);
            }

            Nette\Database\Helpers::loadFromFile($connection, $_default_sql_dir.'/cwp_data.sql');

            return true;
        }

        return false;
    }
}

class MediaDB
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

class mysql extends MediaDB
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
class sqlite extends MediaDB
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
