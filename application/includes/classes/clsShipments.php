<?php

/**
 * clsStockMaster
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
// If it's going to need the database, then it's
// probably smart to require it before we start.
class clsShipments {

    // table name
    protected static $table_name = "shipments";
    //db fileds
    protected static $db_fields = array('reference_number', 'item_id', 'manufacturer', 'shipment_date', 'shipment_quantity', 'stk_id', 'procured_by', 'status', 'created_date', 'created_by', 'modified_by');
    //pk stock id
    public $reference_number;
    //transaction date
    public $item_id;
    //transaction date
    public $manufacturer;
    //transaction num
    public $shipment_date;
    //transaction type id
    public $shipment_quantity;
    //transaction ref
    public $stk_id;
    //from warehouse id
    public $procured_by;
    //from status
    public $status;
    //to warehouse id
    public $created_date;
    //created by
    public $created_by;
    //created on
    public $modified_by;

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
        $strSql = "SELECT * FROM " . static::$table_name . " WHERE PkStockID={$id} LIMIT 1";
        //query result
        $result_array = static::find_by_sql($strSql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    /**
     * 
     * find_by_id
     * @param type $id
     * @return type
     * 
     * 
     */
    public function get_shipment_by_id($id = 0) {

        //select query
        $strSql = "SELECT * FROM " . static::$table_name . " WHERE pk_id={$id} LIMIT 1";
        //query result
        $result_array = static::find_by_sql($strSql);
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    /**
     * 
     * find_by_trans_no
     * @param type $trans_no
     * @return type
     * 
     * 
     */
    public function find_by_trans_no($trans_no = '') {
        //select query
        $result_array = static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE TranNo='{$trans_no}' LIMIT 1 DESC");
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
        return isset($this->PkStockID) ? $this->update() : $this->create();
    }

    /**
     *
     * create
     * @global type $database
     * @return boolean
     * 
     * 
     */
    public function create() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - INSERT INTO table (key, key) VALUES ('value', 'value')
        // - single - quotes around all values
        // - escape all values to prevent SQL injection
        $attributes = $this->sanitized_attributes();
        //insert query
        $sql = "INSERT INTO " . static::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
//echo $sql;exit;
        if ($database->query($sql)) {
            return $database->insert_id();
        } else {
            return false;
        }
    }

    /**
     * 
     * update
     * @global type $database
     * @return type
     * 
     * 
     */
    public function update() {
        global $database;
        //update query
        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= " temp=0";
        $sql .= " WHERE PkStockID=" . $database->escape_value($this->PkStockID);
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
        if (!$this->stockExists() && ($this->PkStockID)) {
            //delete query
            $sql = "DELETE FROM " . static::$table_name;
            $sql .= " WHERE PkStockID=" . $database->escape_value($this->PkStockID);
            $sql .= " LIMIT 1";
            $database->query($sql);
            return ($database->affected_rows() == 1) ? true : false;

            // NB: After deleting, the instance of User still
            // exists, even though the database entry does not.
            // This can be useful, as in:
            // but, for example, we can't call $user->update()
            // after calling $user->delete().
        }
    }

    function ShipmentSearch($type, $wh_id, $groupby = '', $page_type = '') {

        if ($page_type == 'summary') {
            $detail_column = " (shipments.shipment_quantity) as shipment_quantity ";
        } else {
            $detail_column = " shipments.shipment_quantity";
        }
        //select query
        $strSql = "SELECT
                    shipments.pk_id,
                    shipments.shipment_date,
                    shipments.reference_number,
                     $detail_column,
                    
                    sum(tbl_stock_detail.Qty) as received_qty,     
                    itminfo_tab.itm_name,
                    tbl_warehouse.wh_name as stkname,
                    shipments.`status`,
                    itminfo_tab.qty_carton,
		    tbl_itemunits.UnitType,
                    tbl_locations.LocName as procured_by
                    FROM
                    shipments
                    INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
                    INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
                    LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
                    INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
                    LEFT JOIN tbl_stock_master ON tbl_stock_master.shipment_id = shipments.pk_id
                    LEFT JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                    ";
        if(isset($_SESSION['user_level']) && $_SESSION['user_level'] == 2)
        {
            $strSql.= " INNER JOIN funding_stk_prov ON shipments.stk_id = funding_stk_prov.funding_source_id ";
            
            if(isset($_SESSION['user_province1']))
            {
                $where[] = " funding_stk_prov.province_id = ".$_SESSION['user_province1']." ";
            }
            if(isset($_SESSION['user_stakeholder1']))
            {
                $where[] = " funding_stk_prov.stakeholder_id = ".$_SESSION['user_stakeholder1']." ";
            }
        }
        
        if (!empty($this->WHID)) {
            $where[] = "shipments.stk_id = '" . $this->WHID . "'";
        }
        if (!empty($this->item_id)) {
            $where[] = "shipments.item_id = '" . $this->item_id . "'";
        }
        if (!empty($this->procured_by)) {
            $where[] = "shipments.procured_by = '" . $this->procured_by . "'";
        }
        if (!empty($this->status)) {
            $where[] = "shipments.status = '" . $this->status . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "DATE_FORMAT(shipments.shipment_date, '%Y-%m-%d') BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }



        if (!empty($where) && is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }
        //$strSql = $strSql . ' GROUP BY tbl_stock_master.TranNo ORDER BY tbl_stock_master.TranNo DESC';
        $groupby = !empty($groupby) ? $groupby : ' ';
        $strSql = $strSql . $groupby;
        $strSql = $strSql . ' ORDER BY shipments.shipment_date DESC ';


        //echo $strSql;exit;
        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * find_by_id
     * @param type $id
     * @return type
     * 
     * 
     */
    public function getReceivedVouhcers($id = 0) {

        //select query
        $strSql = "SELECT
                            tbl_stock_master.PkStockID,
                            tbl_stock_master.TranNo
                    FROM
                            tbl_stock_master
                    WHERE
                            tbl_stock_master.shipment_id = $id 
                            AND tbl_stock_master.temp = 0";
        
        //query result
        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        $result = array();
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_assoc($rsSql)) {
                $voucher_id = $row['PkStockID'];
                $voucher_no = $row['TranNo'];
                $result[] = "<a onclick=window.open('printReceive.php?id=$voucher_id','_blank','scrollbars=1,width=842,height=595') href=javascript:void(0)>$voucher_no</a>";
            }
        }
        return implode("<br>", $result);
    }

}

?>