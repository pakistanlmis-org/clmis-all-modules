<?php

/**
 * database
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class MySQLDatabase {

    public $last_query;
    private $magic_quotes_active;
    private $real_escape_string_exists;

    public function query($sql) {
        $this->last_query = $sql;
        $result = mysql_query($sql);
        $this->confirm_query($result);
        return $result;
    }

    public function escape_value($value) {
        if ($this->real_escape_string_exists) { // PHP v4.3.0 or higher
            // undo any magic quote effects so mysql_real_escape_string can do the work
            if ($this->magic_quotes_active) {
                $value = stripslashes($value);
            }
            $value = mysql_real_escape_string($value);
        } else { // before PHP v4.3.0
            // if magic quotes aren't already on then add slashes manually
            if (!$this->magic_quotes_active) {
                $value = addslashes($value);
            }
            // if magic quotes are active, then the slashes already exist
        }
        return $value;
    }

    // "database-neutral" methods
    public function fetch_array($result_set) {
        return mysql_fetch_array($result_set);
    }

    public function num_rows($result_set) {
        return mysql_num_rows($result_set);
    }

    public function insert_id() {
        // get the last id inserted over the current db connection
        return mysql_insert_id();
    }

    public function affected_rows() {
        return mysql_affected_rows();
    }

    private function confirm_query($result) {
        if (!$result) {
            //error msg
            $output = "Database query failed: " . mysql_error() . "<br /><br />";
            die($output);
        }
    }

}

$database = new MySQLDatabase();
?>