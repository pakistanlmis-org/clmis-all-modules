<?php

/**
 * clsComments
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.irg>
 * 
 * @version    2.2
 * 
 */
// If it's going to need the database, then it's
// probably smart to require it before we start.
class clsComments {

    // table name
    protected static $table_name = "dashboard_comments";
    //db fileds
    protected static $db_fields = array('dashlet_id', 'dashboard_id', 'stakeholder_id', 'location_id','month_year', 'comments');
    //pk stock id
    public $dashlet_id;
    //transaction date
    public $dashboard_id;
    //transaction date
    public $stakeholder_id;
    //transaction num
    public $location_id;
    //transaction type id
    public $comments;

    public $month_year;
    //transaction ref
    public $pk_id;
  

    // Common Database Methods
    /**
     * 
     * find_all
     * @return type
     * 
     * 
     */
    public function find_all() {
        return static::find_by_sql("SELECT * FROM " . static::$table_name);
    }

    /**
     * 
     * find_by_id
     * @param type $id
     * @return type
     * 
     * 
     */
    public function find_by_id($id = 0) {
        //select query
        $strSql = "SELECT * FROM " . static::$table_name . " WHERE pk_id={$id} LIMIT 1";
        //query result
        $result_array = static::find_by_sql($strSql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    /**
     * 
     * find_by_sql
     * @param type $sql
     * @return type
     * 
     * 
     */
    public function find_by_sql($sql = "") {
        $result_set = mysql_query($sql);
        //query result
        $object_array = array();
        while ($row = mysql_fetch_array($result_set)) {
            $object_array[] = static::instantiate($row);
        }
        return $object_array;
    }

    /**
     * 
     * count_all
     * @global type $database
     * @return type
     * 
     * 
     */
    public function count_all() {
        global $database;
        //select query
        $sql = "SELECT COUNT(*) FROM " . static::$table_name;
        //query result
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    /**
     * 
     * instantiate
     * @param type $record
     * @return \self
     * 
     * 
     */
    private function instantiate($record) {
        // Could check that $record exists and is an array
        $object = new self;
        // Simple, long - form approach:
        // More dynamic, short - form approach:
        foreach ($record as $attribute => $value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    /**
     * 
     * has_attribute
     * @param type $attribute
     * @return type
     * 
     * 
     */
    private function has_attribute($attribute) {
        // We don't care about the value, we just want to know if the key exists
        // Will return true or false
        return array_key_exists($attribute, $this->attributes());
    }

    /**
     * 
     * attributes
     * @return type
     * 
     * 
     */
    protected function attributes() {
        // return an array of attribute names and their values
        $attributes = array();
        foreach (static::$db_fields as $field) {
            if (property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    /**
     * 
     * sanitized_attributes
     * @global type $database
     * @return type
     * 
     * 
     */
    protected function sanitized_attributes() {
        global $database;
        $clean_attributes = array();
        // sanitize the values before submitting
        // Note: does not alter the actual value of each attribute
        foreach ($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $database->escape_value($value);
        }
        return $clean_attributes;
    }

    /**
     * 
     * save
     * @return type
     * 
     * 
     */
    public function save() {
        // A new record won't have an id yet.
        return isset($this->pk_id) ? $this->update() : $this->create();
    }

    /**
     * create
     * @global type $database
     * @return boolean
     */
    public function create() {
            global $database;
            // Don't forget your SQL syntax and good habits:
            // - INSERT INTO table (key, key) VALUES ('value', 'value')
            // - single - quotes around all values
            // - escape all values to prevent SQL injection
            $attributes = $this->sanitized_attributes();

            $sql = "INSERT INTO " . static::$table_name . " (";
            $sql .= join(", ", array_keys($attributes));
            $sql .= ") VALUES ('";
            $sql .= join("', '", array_values($attributes));
            $sql .= "')";
            if ($database->query($sql)) {
                return $database->insert_id();
            } else {
                return false;
            }
    }

    /**
     * update
     * @global type $database
     * @return type
     */
    public function update() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - UPDATE table SET key = 'value', key = 'value' WHERE condition
        // - single - quotes around all values
        // - escape all values to prevent SQL injection
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach ($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE pk_id=" . $database->escape_value($this->pk_id);
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    /**
     * 
     * delete
     * @global type $database
     * @return type
     * 
     * 
     */
    public function delete() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - DELETE FROM table WHERE condition LIMIT 1
        // - escape all values to prevent SQL injection
        // - use LIMIT 1
        //delete query
        $sql = "DELETE FROM " . static::$table_name;
        $sql .= " WHERE pk_id=" . $database->escape_value($this->pk_id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    function GetCommentsById() {
        //select query
        $strSql = "SELECT
	dash.resource_name AS dashboard_name,
	dashlet.resource_name AS dashlet_name,
	stakeholder.stkname,
	tbl_locations.LocName,
	dashboard_comments.comments,
	dashboard_comments.dashlet_id,
	dashboard_comments.dashboard_id,
	dashboard_comments.stakeholder_id,
	dashboard_comments.location_id,
	DATE_FORMAT(dashboard_comments.month_year,'%Y-%m') month_year
FROM
	dashboard_comments
LEFT JOIN resources AS dashlet ON dashboard_comments.dashlet_id = dashlet.pk_id
LEFT JOIN resources AS dash ON dashboard_comments.dashboard_id = dash.pk_id
LEFT JOIN stakeholder ON dashboard_comments.stakeholder_id = stakeholder.stkid
LEFT JOIN tbl_locations ON dashboard_comments.location_id = tbl_locations.PkLocID
WHERE
	dashboard_comments.pk_id = ".$this->pk_id."
ORDER BY
	dashboard_name ASC,
	dashlet_name ASC ";
        $rsSql = mysql_query($strSql) or die("Error GetCommentsById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>