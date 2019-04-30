<?php

/**
 * clsWarehouseData
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsWarehouseData {

    //warehousse id
    public $w_id;
    //report month
    public $report_month;
    //report year
    public $report_year;
    //item id
    public $item_id;
    //item Rec id
    public $itmrec_id;
    //warehouse id
    public $wh_id;
    //warehouse ob a
    public $wh_obl_a;
    //warehouse received
    public $wh_received;
    //warehouse issue up
    public $wh_issue_up;
    //warehouse cb a
    public $wh_cbl_a;
    //wastages
    public $wastages;
    //vials used
    public $vials_used;
    //warehouse adjustment
    public $wh_adja;
    //report date
    public $RptDate;
    //n expiry
    public $n_expiry;
    //created by
    public $created_by;
    //created on
    public $created_on;
    //detail id
    public $detail_id;
    //table name
    protected static $table_name = "tbl_wh_data";
    //db name
    protected static $db_fields = array('report_month', 'report_year', 'item_id', 'wh_id', 'wh_obl_a', 'wh_received', 'wh_issue_up', 'wh_cbl_a', 'wastages', 'vials_used', 'wh_adja', 'RptDate', 'n_expiry', 'created_by', 'created_on', 'add_date', 'last_update', 'ip_address');

    /**
     * getLastReport
     * @param type $wh_id
     * @param type $RptDate
     * @param type $itemId
     * @return boolean
     */
    function getLastReport($wh_id, $RptDate, $itemId) {
        //select query
        //gets
        //warehouse id
        //received
        //issue up
        //warehouse adjustment
        //warehouse cb a
        //warehouse ob a
        $strSql = "SELECT
		tbl_wh_data.w_id,
		tbl_wh_data.wh_received,
		tbl_wh_data.wh_issue_up,
		tbl_wh_data.wh_adja,
		tbl_wh_data.wh_cbl_a,
		tbl_wh_data.wh_obl_a
		FROM
		tbl_wh_data
		WHERE
		tbl_wh_data.wh_id = " . $wh_id . " AND
		tbl_wh_data.RptDate = '" . $RptDate . "' AND 
		tbl_wh_data.item_id = '" . $itemId . "'";
        $rsSql = mysql_query($strSql) or die("getLastReport");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_object($rsSql);
            return array(
                "w_id" => $row->w_id,
                "wh_received" => $row->wh_received,
                "wh_issue_up" => $row->wh_issue_up,
                "wh_adja" => $row->wh_adja,
                "wh_cbl_a" => $row->wh_cbl_a,
                "wh_obl_a" => $row->wh_obl_a
            );
        } else {
            return FALSE;
        }
    }

    /**
     * save
     * @return type
     */
    public function save() {
        // A new record won't have an id yet.
        return isset($this->w_id) ? $this->update() : $this->create();
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
        $sql .= " WHERE w_id=" . $database->escape_value($this->w_id);

        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    /**
     * delete
     * @global type $database
     * @return type
     */
    public function delete() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - DELETE FROM table WHERE condition LIMIT 1
        // - escape all values to prevent SQL injection
        // - use LIMIT 1
        $sql = "DELETE FROM " . static::$table_name;
        $sql .= " WHERE w_id=" . $database->escape_value($this->w_id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;

        // NB: After deleting, the instance of User still
        // exists, even though the database entry does not.
        // This can be useful, as in:
        // but, for example, we can't call $user->update()
        // after calling $user->delete().
    }

    /**
     * instantiate
     * @param type $record
     * @return \self
     */
    private static function instantiate($record) {
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
     * has_attribute
     * @param type $attribute
     * @return type
     */
    private function has_attribute($attribute) {
        // We don't care about the value, we just want to know if the key exists
        // Will return true or false
        return array_key_exists($attribute, $this->attributes());
    }

    /**
     * attributes
     * @return type
     */
    protected function attributes() {
        // return an array of attribute names and their values
        $attributes = array();
        foreach (static::$db_fields as $field) {
            if ($field != 'TranRef' && $field != 'BatchQty') {
                if (property_exists($this, $field)) {
                    if (!empty($this->$field)) {
                        $attributes[$field] = $this->$field;
                    }
                }
            }
        }
        return $attributes;
    }

    /**
     * sanitized_attributes
     * @global type $database
     * @return type
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

    function getMonthYearByWHID($wh_id) {
        $strSql = "SELECT DISTINCT
		tbl_wh_data.report_month,
		tbl_wh_data.report_year,
		tbl_warehouse.locid
		FROM
		tbl_wh_data
		INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
		WHERE
		tbl_wh_data.wh_id = $wh_id
		ORDER BY
		tbl_wh_data.RptDate DESC";
        $rsSql = mysql_query($strSql) or die("getMonthYearByWHID");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * closingBalanceOfMonth
     * @param type $month
     * @param type $year
     * @param type $wh_id
     * @param type $item_id
     * @return string
     */
    function closingBalanceOfMonth($month, $year, $wh_id, $item_id) {
        $strSql = "SELECT
		ifnull(tbl_wh_data.wh_cbl_a,0) as cbv
		FROM
		tbl_wh_data
		WHERE
		tbl_wh_data.item_id='" . $item_id . "' AND
		tbl_wh_data.report_month = $month AND
		tbl_wh_data.report_year = $year AND 
		tbl_wh_data.wh_id = $wh_id";
        $rsSql = mysql_query($strSql) or die("openingBalance");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_object($rsSql);
            return $row->cbv;
        } else {
            return '0';
        }
    }

    /**
     * getDataByWhId
     * @return boolean
     */
    function getDataByWhId() {
        $strSql = "SELECT tbl_wh_data.wh_id from tbl_wh_data
		WHERE wh_id=" . $this->m_wh_id . " LIMIT 1";
        $rsSql = mysql_query($strSql) or die("getDataByWhId");
        if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * adjustReport
     * @return boolean
     */
    function adjustReport() {
        $strSql = "SELECT DISTINCT
            tbl_stock_master.CreatedBy,
            MONTH(tbl_stock_master.TranDate) AS in_month,
            YEAR(tbl_stock_master.TranDate) AS in_year,
            stock_batch.item_id,
            stock_batch.wh_id
            FROM
                    tbl_stock_master
            INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
            INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id";
        $result = mysql_query($strSql);
        while ($row = mysql_fetch_object($result)) {
            $str_sql2 = "Select REPUpdateData(" . $row->in_month . "," . $row->in_year . "," . $row->item_id . "," . $row->wh_id . "," . $row->CreatedBy . ") from DUAL";
            mysql_query($str_sql2);
        }

        return true;
    }

    /**
     * getStockReportParams
     * @return boolean
     */
    function getStockReportParams() {
        $str_sql = "SELECT DISTINCT
                tbl_stock_master.CreatedBy,
                MONTH(tbl_stock_master.TranDate) AS in_month,
                YEAR(tbl_stock_master.TranDate) AS in_year,
                stock_batch.item_id,
                stock_batch.wh_id,
				itminfo_tab.itmrec_id
        FROM
                tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
		INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        WHERE
        tbl_stock_detail.PkDetailID = $this->detail_id";
        $result = mysql_query($str_sql);
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_object($result);
            return array(
                'month' => $row->in_month,
                'year' => $row->in_year,
                'item_id' => $row->item_id,
                'wh_id' => $row->wh_id,
                'created_by' => $row->CreatedBy,
                'itmrec_id' => $row->itmrec_id
            );
        } else {
            return false;
        }
    }

    /**
     * adjustStockReport
     */
    function adjustStockReport() {
        $str_sql2 = "Select REPUpdateData(" . $this->report_month . "," . $this->report_year . "," . $this->item_id . "," . $this->wh_id . "," . $this->created_by . ",'" . $this->itmrec_id . "') from DUAL";
        mysql_query($str_sql2);
    }

    /**
     * addReport
     * @param type $stockId
     * @param type $type
     * @param type $from
     * @param type $uc_wh_id
     */
    function addReport($stockId, $type, $from = null, $uc_wh_id = null) {
        
        $objStockMaster = new clsStockMaster();
        if ($from == 'wh') {
            $result = $objStockMaster->getDateAndItemfromStock($stockId);
        } else {
            $result = $objStockMaster->getItemDetailfromStock($stockId);
        }
        if ($result != FALSE) {
            foreach ($result as $stockdata) {
                $tdate = $stockdata['date'];
                $product = $stockdata['item_id'];
                $productrec_id = $stockdata['itemrec_id'];
                $month = dateFormat($tdate, "first day of this month", "m");
                $year = dateFormat($tdate, "first day of this month", "Y");
                $wh_id = $_SESSION['user_warehouse'];

                $TransDate = dateFormat($tdate, "first day of this month", "Y-m-d");
                $newdate = dateFormat($tdate, "-1 month", "d/m/Y");

                switch ($type) {

                    case 10: // Auto Receive for UC warehouses
                        $qty = abs($stockdata['qty']);
                        $cboflastmonth = $this->closingBalanceOfMonth(monthFromDate($newdate), yearFromDate($newdate), $uc_wh_id, $productrec_id);
                        $this->report_month = $month;
                        $this->report_year = $year;
                        $this->item_id = $product;
                        $this->wh_id = $uc_wh_id;
                        $this->wh_obl_a = $cboflastmonth;
                        $this->wh_received = $qty;
                        $this->wh_issue_up = 0;
                        $this->wh_adja = 0;
                        $this->wh_cbl_a = ($cboflastmonth + $qty);
                        $this->RptDate = $rpt = $year . "-" . $month . "-01";
                        $this->created_by = $_SESSION['user_id'];
                        $this->add_date = date("Y-m-d");
                        $this->itemrec_id = $productrec_id;
                        if (!empty($uc_wh_id) && !empty($rpt) && !empty($productrec_id)) {

                            $strSql = "SELECT
					tbl_wh_data.w_id,
					tbl_wh_data.wh_received,
					tbl_wh_data.wh_issue_up,
					tbl_wh_data.wh_adja,
					tbl_wh_data.wh_cbl_a,
					tbl_wh_data.wh_obl_a
					FROM
					tbl_wh_data
					WHERE
					tbl_wh_data.wh_id = " . $uc_wh_id . " AND
					tbl_wh_data.RptDate = '" . $rpt . "' AND 
					tbl_wh_data.item_id = '" . $productrec_id . "'";

                            $rsSql = mysql_query($strSql) or die("getLastReport");
                            if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
                                $row = mysql_fetch_object($rsSql);
                                $report_data = array(
                                    "w_id" => $row->w_id,
                                    "wh_received" => $row->wh_received,
                                    "wh_issue_up" => $row->wh_issue_up,
                                    "wh_adja" => $row->wh_adja,
                                    "wh_cbl_a" => $row->wh_cbl_a,
                                    "wh_obl_a" => $row->wh_obl_a
                                );
                                if (!empty($report_data['w_id'])) {
                                    $this->w_id = $report_data['w_id'];
                                    $this->wh_received = ($report_data['wh_received'] + $this->wh_received);
                                    $this->wh_cbl_a = ($this->wh_obl_a + $this->wh_received - $report_data['wh_issue_up'] + $report_data['wh_adja']);
                                    $updateData = "update tbl_wh_data set report_month=" . $this->report_month . ",report_year=" . $this->report_year . ",item_id='" . $this->itemrec_id . "',wh_id=" . $this->wh_id . ",wh_obl_a=" . $this->wh_obl_a . ",wh_received=" . $this->wh_received . ",wh_issue_up=" . $this->wh_issue_up . ",wh_cbl_a=" . $this->wh_cbl_a . ",wh_adja=" . $this->wh_adja . ",RptDate='" . $this->RptDate . "',created_by=" . $this->created_by . " where w_id=" . $this->w_id . "";
                                    mysql_query($updateData) or die("update to wh_data");
                                } else {
                                    $saveData = "insert into tbl_wh_data set report_month=" . $this->report_month . ",report_year=" . $this->report_year . ",item_id='" . $this->itemrec_id . "',wh_id=" . $this->wh_id . ",wh_obl_a=" . $this->wh_obl_a . ",wh_received=" . $this->wh_received . ",wh_issue_up=" . $this->wh_issue_up . ",wh_cbl_a=" . $this->wh_cbl_a . ",wh_adja=" . $this->wh_adja . ",RptDate='" . $this->RptDate . "',created_by=" . $this->created_by . "";
                                    mysql_query($saveData) or die("save to wh_data");
                                }
                            }
                        }
                        break;
                    default:
                        $strSql = "SELECT REPUpdateData(" . $month . "," . $year . "," . $product . "," . $wh_id . "," . $_SESSION['user_id'] . ",'" . $productrec_id . "') FROM DUAL";
                        //echo $strSql;exit;
                        mysql_query($strSql);
                        break;
                }
            }
        }
    }

    
     function addReport_for_dist_wh($stockId, $type, $from = null, $uc_wh_id = null) {
        $objStockMaster = new clsStockMaster();
        if ($from == 'wh') {
            $result = $objStockMaster->getDateAndItemfromStock($stockId);
        } else {
            $result = $objStockMaster->getItemDetailfromStock($stockId);
        }
        if ($result != FALSE) {
            foreach ($result as $stockdata) {
                $tdate = $stockdata['date'];
                $product = $stockdata['item_id'];
                $productrec_id = $stockdata['itemrec_id'];
                $month = dateFormat($tdate, "first day of this month", "m");
                $year = dateFormat($tdate, "first day of this month", "Y");
                $wh_id = $_SESSION['user_warehouse'];

                $TransDate = dateFormat($tdate, "first day of this month", "Y-m-d");
                $newdate = dateFormat($tdate, "-1 month", "d/m/Y");

                switch ($type) {

                    case 10: // Auto Receive for UC warehouses
                        $qty = abs($stockdata['qty']);
                        $cboflastmonth = $this->closingBalanceOfMonth(monthFromDate($newdate), yearFromDate($newdate), $uc_wh_id, $productrec_id);
                        $this->report_month = $month;
                        $this->report_year = $year;
                        $this->item_id = $product;
                        $this->wh_id = $uc_wh_id;
                        $this->wh_obl_a = $cboflastmonth;
                        $this->wh_received = $qty;
                        $this->wh_issue_up = 0;
                        $this->wh_adja = 0;
                        $this->wh_cbl_a = ($cboflastmonth + $qty);
                        $this->RptDate = $rpt = $year . "-" . $month . "-01";
                        $this->created_by = $_SESSION['user_id'];
                        $this->add_date = date("Y-m-d");
                        $this->itemrec_id = $productrec_id;
                        if (!empty($uc_wh_id) && !empty($rpt) && !empty($productrec_id)) {

                            $strSql = "SELECT
					tbl_wh_data.w_id,
					tbl_wh_data.wh_received,
					tbl_wh_data.wh_issue_up,
					tbl_wh_data.wh_adja,
					tbl_wh_data.wh_cbl_a,
					tbl_wh_data.wh_obl_a
					FROM
					tbl_wh_data
					WHERE
					tbl_wh_data.wh_id = " . $uc_wh_id . " AND
					tbl_wh_data.RptDate = '" . $rpt . "' AND 
					tbl_wh_data.item_id = '" . $productrec_id . "'";

                            $rsSql = mysql_query($strSql) or die("getLastReport");
                            if ($rsSql != FALSE && mysql_num_rows($rsSql) > 0) {
                                $row = mysql_fetch_object($rsSql);
                                $report_data = array(
                                    "w_id" => $row->w_id,
                                    "wh_received" => $row->wh_received,
                                    "wh_issue_up" => $row->wh_issue_up,
                                    "wh_adja" => $row->wh_adja,
                                    "wh_cbl_a" => $row->wh_cbl_a,
                                    "wh_obl_a" => $row->wh_obl_a
                                );
                                if (!empty($report_data['w_id'])) {
                                    $this->w_id = $report_data['w_id'];
                                    $this->wh_received = ($report_data['wh_received'] + $this->wh_received);
                                    $this->wh_cbl_a = ($this->wh_obl_a + $this->wh_received - $report_data['wh_issue_up'] + $report_data['wh_adja']);
                                    $updateData = "update tbl_wh_data set report_month=" . $this->report_month . ",report_year=" . $this->report_year . ",item_id='" . $this->itemrec_id . "',wh_id=" . $this->wh_id . ",wh_obl_a=" . $this->wh_obl_a . ",wh_received=" . $this->wh_received . ",wh_issue_up=" . $this->wh_issue_up . ",wh_cbl_a=" . $this->wh_cbl_a . ",wh_adja=" . $this->wh_adja . ",RptDate='" . $this->RptDate . "',created_by=" . $this->created_by . " where w_id=" . $this->w_id . "";
                                    mysql_query($updateData) or die("update to wh_data");
                                } else {
                                    $saveData = "insert into tbl_wh_data set report_month=" . $this->report_month . ",report_year=" . $this->report_year . ",item_id='" . $this->itemrec_id . "',wh_id=" . $this->wh_id . ",wh_obl_a=" . $this->wh_obl_a . ",wh_received=" . $this->wh_received . ",wh_issue_up=" . $this->wh_issue_up . ",wh_cbl_a=" . $this->wh_cbl_a . ",wh_adja=" . $this->wh_adja . ",RptDate='" . $this->RptDate . "',created_by=" . $this->created_by . "";
                                    mysql_query($saveData) or die("save to wh_data");
                                }
                            }
                        }
                        break;
                    default:
                        $strSql = "SELECT REPUpdateData_for_dist_wh(" . $month . "," . $year . "," . $product . "," . $wh_id . "," . $_SESSION['user_id'] . ",'" . $productrec_id . "') FROM DUAL";
                        //echo $strSql;exit;
                        mysql_query($strSql);
                        break;
                }
            }
        }
    }

}

?>