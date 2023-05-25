<?php


use Nette\Utils\FileSystem;


class MediaUpdate
{
 
    public $table_name;
    public $refresh = false;
    protected $conn;
    
    public function versionUpdate($file)
    {
        include_once($file);

        $updates = ['newTable' => $new_table, 'updateColumns' => $rename_column, 'newColumn' => $new_column, 'newData' => $new_data, 'updateData' => $update_data,];

        foreach ($updates as $classmethod => $data_array) {
            $this->$classmethod($data_array);
        }

        $filename = basename($file);

        if ($this->check_tableExists('updates')) {
            $this->newData(["updates" => ["update_filename" => $filename]]);
        } else {
            $this->setSkipFile($file);
        }
    }



    public function newData($new_data)
    {
        if (is_array($new_data)) {

            foreach ($new_data as $table => $new_data_vals) {

                $this->conn->query('INSERT INTO ' . $table . ' ?', $new_data_vals);
                $this->refresh = true;
            }
        }
    }
    public function __construct($db_conn)
    {
        $this->conn = $db_conn;
    }

    public function check_tableExists($table_name = '')
    {
        if ($table_name != '') {
            $this->table_name = $table_name;
        }

        $query = "SELECT name FROM sqlite_master WHERE type='table' AND name='" . $this->table_name . "'";
        $result = $this->conn->fetchField($query);
        return $result;
    }
    public function newTable($new_table)
    {

        if (is_array($new_table)) {
            foreach ($new_table as $table_name) {
                $this->set($table_name);
                if (!$this->check_tableExists()) {
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

    public function create_table($table_name)
    {
        $sql_file = FileSystem::normalizePath(__SQLITE_DIR__ . "/default" . '/' . "cwp_table_" . $table_name . ".sql");
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
                    if ($this->check_columnExists($old)) {
                        if (!$this->check_columnExists($new)) {

                            $this->rename_column($old, $new);
                            $this->refresh = true;
                        }
                    }
                }
            }
        }
    }

    public function check_columnExists($column)
    {
        $query = "SELECT 1 FROM pragma_table_info('" . $this->table_name . "') where name='" . $column . "'";
        $result = $this->conn->fetchField($query);
        return $result;
    }

    public function rename_column($old, $new)
    {
        $query = "ALTER TABLE " . $this->table_name . " RENAME COLUMN '" . $old . "'  TO '" . $new . "';";
        $result = $this->conn->fetchField($query);
    }

    public function newColumn($new_column)
    {

        if (is_array($new_column)) {
            foreach ($new_column as $table_name => $column) {
                $this->set($table_name);
                foreach ($column as $field => $type) {
                    if (!$this->check_columnExists($field)) {
                        $this->create_column($field, $type);
                        $this->refresh = true;
                    }
                }
            }
        }
    }

    public function create_column($column, $type)
    {
        $query = "ALTER TABLE " . $this->table_name . " ADD " . $column . " " . $type . ";";
        $result = $this->conn->fetchField($query);
    }

    public function updateData($update_data)
    {
        if (is_array($update_data)) {
            foreach ($update_data as $table => $updates) {
                foreach ($updates as $where => $data) {
                    foreach ($data as $key => $update_array) {
                        $query = "UPDATE " . $table . " ";
                        $query = $query . "SET ";
                        foreach ($update_array as $field => $value) {
                            $field_array[] = $field . " = '" . $value . "'";
                        }

                        $query .= implode(",", $field_array);
                        unset($field_array);
                        $query .= " WHERE " . $where . " = '" . $key . "'";
                        $result = $this->conn->query($query);
                        $this->refresh = true;
                    }
                }
            }
        }
    }

    public static function createDatabase()
    {

        if (!file_exists(__SQLITE_DATABASE__)) {
            FileSystem::createDir(__SQLITE_DIR__);
            
            $connection = new Nette\Database\Connection(__DATABASE_DSN__);
            $_default_sql_dir = FileSystem::normalizePath(__SQLLITE_DEFAULT_TABLES_DIR__);
            $file_tableArray = Utils::get_filelist($_default_sql_dir, 'cwp_table.*)\.(sql', 0);
        
        
            foreach ($file_tableArray as $k => $sql_file) {
                $table_name = str_replace("cwp_table_", "", basename($sql_file, ".sql"));
                $connection->query("drop table if exists " . $table_name);
                Nette\Database\Helpers::loadFromFile($connection, $sql_file);
            }
        
            Nette\Database\Helpers::loadFromFile($connection, $_default_sql_dir . '/cwp_data.sql');
        
            return true;
            
        } 

        return false;

    }
}
