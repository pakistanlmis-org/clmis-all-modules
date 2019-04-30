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
class clsStockMaster {

    // table name
    protected static $table_name = "tbl_stock_master";
    //db fileds
    protected static $db_fields = array('TranDate', 'TranNo', 'TranTypeID', 'TranRef', 'WHIDFrom', 'WHIDTo', 'CreatedBy', 'CreatedOn', 'ReceivedRemarks', 'temp', 'trNo', 'LinkedTr', 'issued_by');
    //pk stock id
    public $PkStockID;
    //transaction date
    public $TranDate;
    //transaction num
    public $TranNo;
    //transaction type id
    public $TranTypeID;
    //transaction ref
    public $TranRef;
    //from warehouse id
    public $WHIDFrom;
    //to warehouse id
    public $WHIDTo;
    //created by
    public $CreatedBy;
    //created on
    public $CreatedOn;
    //Received Remarks
    public $ReceivedRemarks;
    //temp
    public $temp;
    //transaction num
    public $trNo;
    //batch num
    public $batch_no;
    //item id
    public $item_id;
    //from date
    public $fromDate;
    //to date
    public $toDate;
    //linked transaction
    public $LinkedTr;
    //stakeholder
    public $stakeholder;
    //province
    public $province;
    //issued by
    public $issued_by;
    //funding source
    public $funding_source;

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
//echo 'Ins:::'.$sql;exit;
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
//echo 'upd:::'.$sql;exit;
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

    /**
     * 
     * stockExists
     * @return boolean
     * 
     * 
     */
    function stockExists() {
        //select query
        $strSql = "SELECT
            tbl_stock_detail.PkDetailID
            FROM
            tbl_stock_detail
            WHERE
            tbl_stock_detail.fkStockID = " . $this->PkStockID;
        //query result
        $rsSql = mysql_query($strSql) or die("Error stockExists");
        if (mysql_num_rows($rsSql) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function GetStockLedger() {
        $whId = $_SESSION['user_warehouse'];

        $fs = '';
        if (!empty($this->funding_source) && $this->funding_source != 'all') {
            $fs = " AND s3_.funding_source = $this->funding_source ";
        }

        //select query
        $strSql = "SELECT
	s0_.PkDetailID AS detail_id,
	s0_.Qty AS quantity,
	s0_.IsReceived AS is_received,
	s0_.adjustmentType AS adjustment_type,
	s0_.fkStockID AS stock_master_id,
	s0_.BatchID AS stock_batch_warehouse_id,
	s0_.fkUnitID AS item_unit_id,
	tbl_trans_type.trans_nature,
	s1_.CreatedOn,
	sysuser_tab.usrlogin_id,
	s1_.TranDate,
	s1_.TranNo,
	s1_.TranTypeID,
	`from`.wh_name fromWh,
	`to`.wh_name toWh,
	s3_.batch_no,
	s3_.batch_id,
	s3_.batch_expiry,
        tbl_trans_type.trans_type,
        s3_.funding_source,
        fs.wh_name as funding_source_name
        FROM
                tbl_stock_detail AS s0_
        INNER JOIN tbl_stock_master AS s1_ ON s0_.fkStockID = s1_.PkStockID
        INNER JOIN stock_batch AS s3_ ON s0_.BatchID = s3_.batch_id
        INNER JOIN tbl_trans_type ON s1_.TranTypeID = tbl_trans_type.trans_id
        INNER JOIN sysuser_tab ON s1_.CreatedBy = sysuser_tab.UserID
        INNER JOIN tbl_warehouse AS `from` ON s1_.WHIDFrom = `from`.wh_id
        INNER JOIN tbl_warehouse AS `to` ON s1_.WHIDTo = `to`.wh_id
        INNER JOIN tbl_warehouse  AS  fs ON s3_.funding_source = fs.wh_id
        WHERE
                s3_.item_id = $this->item_id
        AND s3_.wh_id = $whId
        AND (
                DATE_FORMAT(s1_.TranDate, '%Y-%m-%d') BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'
        )
        $fs
        ORDER BY
	s1_.TranDate ASC,
	adjustment_type ASC,
	quantity ASC";
        //echo '<pre>';print_r($this);
        //echo $strSql;exit;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetStockLedger");
        $data = array();
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_assoc($rsSql)) {
                $data[] = $row;
            }
            return $data;
        } else {
            return false;
        }
    }

    function getBatchOB($batchId, $trDate) {
        list($dd, $mm, $yy) = explode("/", $trDate);
        $trDate1 = $yy . "-" . $mm . "-" . $dd;
        //select query
        $strSql = "SELECT
	SUM(s0_.Qty) AS sclr0
        FROM
                tbl_stock_detail s0_
        INNER JOIN tbl_stock_master s1_ ON s0_.fkStockID = s1_.PkStockID
        INNER JOIN stock_batch s2_ ON s0_.BatchID = s2_.batch_id
        WHERE
                s2_.batch_id = $batchId
            AND DATE_FORMAT(
                s1_.TranDate,
                '%Y-%m-%d'
            ) < '" . $trDate1 . "'
        ORDER BY
                s1_.TranDate ASC";
        //query result
        $rsSql = mysql_query($strSql) or die("Error stockExists");
        $data = array();
        if (mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_assoc($rsSql);
            return $row['sclr0'];
        } else {
            return 0;
        }
    }

    public function getBatchOBCB($type, $item, $trDate, $funding_source = '') {
        $wh_id = $_SESSION['user_warehouse'];

        $fs = '';
        if (!empty($funding_source) && $funding_source != '' && $funding_source != 'all') {
            $fs = " AND s1_.funding_source = $funding_source ";
        }

        $operator = '<';

        if ($type == 'CB') {
            $operator = '<=';
        }

        $strSql = "SELECT
                        SUM(s0_.Qty) AS Qty,
                        s1_.batch_no,
                        s1_.batch_id,
                        tbl_warehouse.wh_name as funding_source_name

                    FROM
                        tbl_stock_detail s0_
                    INNER JOIN tbl_stock_master s3_ ON s0_.fkStockID = s3_.PkStockID
                    INNER JOIN stock_batch s1_ ON s0_.BatchID = s1_.batch_id
                    INNER JOIN itminfo_tab i2_ ON s1_.item_id = i2_.itm_id
                    INNER JOIN tbl_warehouse ON s1_.funding_source = tbl_warehouse.wh_id
                    WHERE
                        i2_.itm_id = $item
                        AND s1_.wh_id = $wh_id
                        AND DATE_FORMAT(s3_.TranDate, '%Y-%m-%d') $operator '" . $trDate . "'
                        $fs
                    GROUP BY
                        s1_.batch_id
                    HAVING
                        Qty <> 0";
        //echo $strSql;exit;
        //query result
        $rsSql = mysql_query($strSql) or die("Error getBatchOBCB");
        $data = array();
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_assoc($rsSql)) {
                $data[] = $row;
            }
            return $data;
        } else {
            return array();
        }
    }

    /**
     * 
     * GetTempStockIssue
     * @param type $user
     * @param type $wh_id
     * @param type $type
     * @return boolean
     * 
     * 
     */
    function GetTempStockIssue($user, $wh_id, $type) {
        //select query
        //gets
        //transaction num,
        //transaction ref,
        //transaction Date,
        //Pk Stock ID,
        //Received Remarks,
        //warehouse name,
        //To warehouse,
        //issued by,
        //funding source
        $strSql = "SELECT
					tbl_stock_master.TranNo,
					tbl_stock_master.TranRef,
					tbl_stock_master.TranDate,
					tbl_stock_master.PkStockID,
					tbl_stock_master.ReceivedRemarks,
					CONCAT(tbl_warehouse.wh_name, ' (', stakeholder.stkname, ')') AS wh_name,
					tbl_stock_master.WHIDTo,
					tbl_stock_master.issued_by,
					stock_batch.funding_source
				FROM
					tbl_stock_master
				INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
				INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
				INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = tbl_stock_master.WHIDTo
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				WHERE
					tbl_stock_master.CreatedBy = $user
				AND tbl_stock_master.WHIDFrom = $wh_id
				AND tbl_stock_master.temp = 1
				AND tbl_stock_master.TranTypeID = $type";

        //query result
        $rsSql = mysql_query($strSql) or die("Error GetTempStockIssue");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetStocksIssueList
     * @param type $userid
     * @param type $wh_id
     * @param type $type
     * @param type $stockid
     * @return boolean
     * 
     * 
     */
    function GetStocksIssueList($userid, $wh_id, $type, $stockid) {
        //select query 
        //gets
        //transaction date
        //qty
        //batch num
        //production date
        //batch expiry
        //item name
        //carton qty
        //warehouse id
        //warehouse name
        //unit type
        //pk stock id
        //warehouse name
        //transaction num
        //transaction  ref
        //received remark
        //issued by
        //pk detail id
        $strSql = "SELECT
					tbl_stock_master.TranDate,
					tbl_stock_detail.Qty,
					stock_batch.batch_no,
					stock_batch.production_date,
					stock_batch.batch_expiry,
					itminfo_tab.itm_name,
					IFNULL(stakeholder_item.quantity_per_pack, itminfo_tab.qty_carton) qty_carton,
					tbl_warehouse.wh_id,
					CONCAT(tbl_warehouse.wh_name, ' (', stakeholder.stkname, ')') AS wh_name,
					tbl_itemunits.UnitType,
					tbl_stock_master.PkStockID,
					tbl_stock_master.TranNo,
					tbl_stock_master.TranRef,
					tbl_stock_master.ReceivedRemarks,
					list_detail.list_value AS issued_by,
					tbl_stock_detail.PkDetailID
				FROM
					tbl_stock_master
				INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
				INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                                LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
				INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
				INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
				LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
				INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
				LEFT JOIN list_detail ON tbl_stock_master.issued_by = list_detail.pk_id
				WHERE
					tbl_stock_detail.temp = 0 AND
					tbl_stock_master.temp = 0 AND
					tbl_stock_master.WHIDFrom = '" . $wh_id . "' AND
					tbl_stock_master.CreatedBy = " . $userid . " AND 
					tbl_stock_master.TranTypeID =" . $type . " AND tbl_stock_master.PkStockID = $stockid
				ORDER BY
					itminfo_tab.frmindex ASC,
					stock_batch.batch_expiry ASC,
					stock_batch.batch_no ASC";
        //echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error GetStocksIssueList");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * updateTemp
     * @param type $id
     * @return boolean
     * 
     * 
     */
    function updateTemp($id) {
        //update query
        $strSql = "Update tbl_stock_master set temp=0 where PkStockID=$id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error updateTemp");
        if (mysql_affected_rows() > 0) {
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetTempStocksIssueList
     * @param type $userid
     * @param type $wh_id
     * @param type $type
     * @return boolean
     * 
     * 
     */
    function GetTempStocksIssueList($userid, $wh_id, $type) {
        //select query
        //gets
        //transaction date
        //qty
        //batch num
        //unit price
        //batch expiry
        //item name
        //carton qty
        //waerhouse name
        //unit type
        //pk stock id
        //transaction num
        //transaction ref
        //pk detail id
        $strSql = "SELECT
        tbl_stock_master.TranDate,
        tbl_stock_detail.Qty,
        stock_batch.batch_no,
		stock_batch.unit_price,
        stock_batch.batch_expiry,
        itminfo_tab.itm_name,
		itminfo_tab.qty_carton,
        CONCAT(tbl_warehouse.wh_name, ' (', stakeholder.stkname, ')') AS wh_name,
        tbl_itemunits.UnitType,
        tbl_stock_master.PkStockID,
        tbl_stock_master.TranNo,
        tbl_stock_master.TranRef,
		tbl_stock_detail.PkDetailID
        FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
        LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
        WHERE
        tbl_stock_detail.temp = 1 AND
        tbl_stock_master.temp = 1 AND
        tbl_stock_master.WHIDFrom = '" . $wh_id . "' AND
        tbl_stock_master.CreatedBy = " . $userid . " AND 
		tbl_stock_master.TranTypeID =" . $type . " 
		ORDER BY itminfo_tab.frmindex ASC";


        $rsSql = mysql_query($strSql) or die("Error GetTempStocksIssueList");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetTempStockReceive
     * @param type $user
     * @param type $wh_id
     * @param type $type
     * @return boolean
     * 
     * 
     */
    function GetTempStockReceive($user, $wh_id, $type) {
        //select query
        //gets
        //pk stock id
        //transaction num
        //transaction ref
        //transaction date
        //pk stock id
        //warehouse name
        //from warehouse
        $strSql = "SELECT
			tbl_stock_master.PkStockID,
			tbl_stock_master.TranNo,
			tbl_stock_master.TranRef,
			tbl_stock_master.TranDate,
			tbl_stock_master.PkStockID,
			tbl_warehouse.wh_name,
			tbl_stock_master.WHIDFrom
		FROM
			tbl_stock_master
		INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = tbl_stock_master.WHIDTo
		WHERE
			tbl_stock_master.CreatedBy = $user
		AND tbl_stock_master.WHIDTo = $wh_id
		AND tbl_stock_master.temp = 1
		AND tbl_stock_master.TranTypeID = $type";
        //query result
        //echo $strSql;
        $rsSql = mysql_query($strSql) or die("Error GetTempStockReceive");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetTempStockRUpdate
     * @param type $PkStockID
     * @return boolean
     * 
     * 
     */
    function GetTempStockRUpdate($PkStockID = 0) {
        //select query
        //gets
        //pk stock id
        //transaction num
        //transaction date
        //pk stock id
        //warehouse name
        //item id
        //from warehouse
        $strSql = "SELECT
			tbl_stock_master.PkStockID,
			tbl_stock_master.TranNo,
			tbl_stock_master.TranRef,
			tbl_stock_master.TranDate,
			tbl_stock_master.PkStockID,
			tbl_warehouse.wh_name,
			itminfo_tab.itm_id,
			tbl_stock_master.WHIDFrom
		FROM
			tbl_stock_master
		INNER JOIN tbl_warehouse ON tbl_warehouse.wh_id = tbl_stock_master.WHIDFrom
		 INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
		INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
		WHERE tbl_stock_master.PkStockID = $PkStockID";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetTempStockRUpdate");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetTempStocksReceiveList
     * @param type $userid
     * @param type $wh_id
     * @param type $type
     * @return boolean
     * 
     * 
     */
    function GetTempStocksReceiveList($userid, $wh_id, $type) {
        $strSql = "
        SELECT
			tbl_stock_master.TranDate,
			tbl_stock_detail.Qty,
			stock_batch.manufacturer,
			stock_batch.batch_no,
			stock_batch.production_date,
			stock_batch.batch_expiry,
			itminfo_tab.itm_name,
			itminfo_tab.qty_carton,
			tbl_warehouse.wh_name,
			tbl_stock_master.PkStockID,
			tbl_stock_master.TranNo,
			tbl_stock_master.TranRef,
			tbl_stock_detail.PkDetailID,
			tbl_itemunits.UnitType
		FROM
			tbl_stock_master
		INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
		LEFT JOIN stakeholder ON tbl_stock_detail.manufacturer = stakeholder.stkid
		LEFT JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
		INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
		INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
		INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
		LEFT JOIN tbl_itemunits ON  itminfo_tab.itm_type= tbl_itemunits.UnitType
		WHERE
			tbl_stock_detail.temp = 1 AND
			tbl_stock_master.temp = 1 AND
			tbl_stock_master.WHIDTo = '" . $wh_id . "' AND
			tbl_stock_master.CreatedBy = " . $userid . " AND 
			tbl_stock_master.TranTypeID =" . $type . "
		ORDER BY stock_batch.batch_no ASC ";

        //echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error GetTempStocksReceiveList");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetLastInseredTempStocksReceiveList
     * @param type $userid
     * @param type $wh_id
     * @param type $type
     * @return boolean
     * 
     * 
     */
    function GetLastInseredTempStocksReceiveList($userid, $wh_id, $type) {
        $strSql = "SELECT
        tbl_stock_master.TranDate,
        tbl_stock_detail.Qty,
        tbl_stock_detail.manufacturer,
        stock_batch.batch_no,
        stock_batch.production_date,
		stock_batch.batch_expiry,
        itminfo_tab.itm_name,
        tbl_warehouse.wh_name,
        tbl_stock_master.PkStockID,
        tbl_stock_master.TranNo,
        tbl_stock_master.TranRef,
		tbl_stock_detail.PkDetailID,
		itminfo_tab.itm_id,
		stock_batch.unit_price
        FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
        WHERE
        tbl_stock_detail.temp = 1 AND
        tbl_stock_master.temp = 1 AND
        tbl_stock_master.WHIDTo = '" . $wh_id . "' AND
        tbl_stock_master.CreatedBy = " . $userid . " AND 
		tbl_stock_master.TranTypeID =" . $type . "
		ORDER BY tbl_stock_detail.PkDetailID DESC LIMIT 1 ";
        $rsSql = mysql_query($strSql) or die("Error :" . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetStocksReceiveList
     * @param type $userid
     * @param type $wh_id
     * @param type $type
     * @param type $stockid
     * @return boolean
     * 
     * 
     */
    function GetStocksReceiveList($userid, $wh_id, $type, $stockid) {
        $strSql = "SELECT
        tbl_stock_master.TranDate,
        tbl_stock_detail.Qty,
        stock_batch.batch_no,
        stock_batch.production_date,
		stock_batch.batch_expiry,
        itminfo_tab.itm_name,
		IFNULL(stakeholder_item.quantity_per_pack, itminfo_tab.qty_carton) qty_carton,
        tbl_warehouse.wh_name,
        tbl_stock_master.PkStockID,
        tbl_stock_master.TranNo,
        tbl_stock_master.TranRef,
        tbl_itemunits.UnitType,
		tbl_stock_detail.PkDetailID,
		stakeholder.stk_type_id
        FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
        LEFT JOIN tbl_itemunits ON  itminfo_tab.itm_type= tbl_itemunits.UnitType
        WHERE
        tbl_stock_master.WHIDTo = '" . $wh_id . "' AND
        tbl_stock_master.CreatedBy = " . $userid . " AND 
		tbl_stock_master.TranTypeID =" . $type . " AND tbl_stock_master.PkStockID = $stockid
		ORDER BY itminfo_tab.frmindex ASC,
		stock_batch.batch_no ASC";
        $rsSql = mysql_query($strSql) or die("Error GetStocksReceiveList");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * StockAdjustmentSearch
     * @return boolean
     * 
     * 
     */
    function StockAdjustmentSearch() {
        $strSql = "SELECT
        tbl_stock_master.TranDate,
        tbl_stock_master.TranNo,
        tbl_stock_master.TranRef,
         tbl_stock_master.PkStockID,
        itminfo_tab.itm_name,
        stock_batch.batch_no,
        tbl_stock_detail.Qty,
        tbl_stock_master.ReceivedRemarks,
        tbl_trans_type.trans_type,
        itminfo_tab.itm_type,
        itminfo_tab.qty_carton
        FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id";
        if (!empty($this->TranNo)) {
            $where[] = "tbl_stock_master.TranNo LIKE '%" . $this->TranNo . "%'";
        }
        if (!empty($this->TranTypeID)) {
            $where[] = "tbl_stock_master.TranTypeID = '" . $this->TranTypeID . "'";
        }
        if (!empty($this->WHIDFrom)) {
            $where[] = "tbl_stock_master.WHIDFrom = '" . $this->WHIDFrom . "'";
        }
        if (!empty($this->WHIDTo)) {
            $where[] = "tbl_stock_master.WHIDTo = '" . $this->WHIDTo . "'";
        }
        if (!empty($this->item_id)) {
            $where[] = "stock_batch.item_id = '" . $this->item_id . "'";
        }
        if (!empty($this->batch_id)) {
            $where[] = "stock_batch.batch_id = '" . $this->batch_id . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "tbl_stock_master.TranDate BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }

        if (is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }

        $strSql .= " GROUP BY tbl_stock_master.TranNo ORDER BY tbl_stock_master.TranNo DESC";
        $rsSql = mysql_query($strSql) or die("Error StockAdjustmentSearch");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * StockAdjustmentSearchList
     * @param type $stockpkid
     * @return boolean
     * 
     * 
     */
    function StockAdjustmentSearchList($stockpkid) {
        $strSql = "SELECT
        tbl_stock_master.TranDate,
        tbl_stock_master.TranNo,
        tbl_stock_master.TranRef,
         tbl_stock_master.PkStockID,
        itminfo_tab.itm_name,
        itminfo_tab.qty_carton,
        stock_batch.batch_no,
		stock_batch.batch_expiry,
        tbl_stock_detail.Qty,
        tbl_stock_master.ReceivedRemarks,
        tbl_trans_type.trans_type,
        itminfo_tab.itm_type,
		tbl_warehouse.wh_name
        FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
		INNER JOIN tbl_warehouse ON stock_batch.wh_id = tbl_warehouse.wh_id";

        $strSql .= " WHERE tbl_stock_master.PkStockID = " . $stockpkid . " ORDER BY tbl_stock_master.TranNo DESC";
        $rsSql = mysql_query($strSql) or die("Error StockAdjustmentSearch");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * StockSearch
     * @param type $type
     * @param type $wh_id
     * @return boolean
     * 
     * 
     */
    function StockSearch($type, $wh_id, $groupby = '', $page_type = '') {
        if ($page_type == 'summary') {
            $detail_column = "SUM(tbl_stock_detail.Qty) as Qty";
        } else {
            $detail_column = "tbl_stock_detail.Qty";
        }
        //select query
        $strSql = "SELECT
					tbl_stock_master.TranDate,
					tbl_stock_master.PkStockID,
					tbl_stock_master.TranNo,
					tbl_stock_master.TranRef,
					tbl_warehouse.wh_name,
					itminfo_tab.itm_name,
					stock_batch.batch_no,
					$detail_column,
					tbl_itemunits.UnitType,
					tbl_stock_detail.PkDetailID,
					stock_batch.batch_id AS BatchID,
					stock_batch.batch_expiry,
					stakeholder.stkname,
                                         IFNULL(stakeholder_item.quantity_per_pack, itminfo_tab.qty_carton) qty_carton,
                                         itminfo_tab.field_color
				FROM
					tbl_stock_master
					INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
					INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
					INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
					INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
					LEFT JOIN tbl_itemunits ON itminfo_tab.item_unit_id = tbl_itemunits.pkUnitID
					LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                    LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid";

        if (!empty($this->TranNo)) {
            $where[] = "tbl_stock_master.TranNo LIKE '%" . $this->TranNo . "%'";
        }
        if (!empty($this->batch_no)) {
            $where[] = "stock_batch.batch_no LIKE '%" . $this->batch_no . "%'";
        }
        if (!empty($this->TranRef)) {
            $where[] = "tbl_stock_master.TranRef LIKE '%" . $this->TranRef . "%'";
        }
        if (!empty($this->WHIDFrom)) {
            $where[] = "tbl_stock_master.WHIDFrom = '" . $this->WHIDFrom . "'";
        }
        if (!empty($this->item_id)) {
            $where[] = "stock_batch.item_id = '" . $this->item_id . "'";
        }
        if (!empty($this->manufacturer)) {
            $where[] = "tbl_stock_detail.manufacturer = '" . $this->manufacturer . "'";
        }
        if (!empty($this->WHIDTo)) {
            $where[] = "stock_batch.funding_source = '" . $this->WHIDTo . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }

        $where[] = "tbl_stock_master.TranTypeID = $type";
        $where[] = "stock_batch.wh_id = $wh_id";
        $where[] = "tbl_stock_detail.temp = 0";

        if (is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }
        $groupby = !empty($groupby) ? $groupby : ' ORDER BY tbl_stock_master.TranNo DESC';
        $strSql = $strSql . $groupby;

        //$strSql = $strSql . ' GROUP BY tbl_stock_master.TranNo ORDER BY tbl_stock_master.TranNo DESC';
        //$strSql = $strSql . ' ORDER BY tbl_stock_master.TranNo DESC';
        //echo $strSql;
        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * StockSearchList
     * @param type $type
     * @param type $wh_id
     * @return boolean
     * 
     * 
     */
    function StockSearchList($type, $wh_id) {
        //select query
        //gets
        //transaction date
        //pk stock id
        //transaction num
        //warehouse name
        //batch num
        //batch expiry
        //qty
        //item type
        //pk detail id
        //batch id
        //item id
        //item name
        //carton qty
        $strSql = "SELECT
	tbl_stock_master.TranDate,
	tbl_stock_master.PkStockID,
	tbl_stock_master.TranNo,
	tbl_stock_master.TranRef,
	tbl_warehouse.wh_name,
	stock_batch.batch_no,
	stock_batch.batch_expiry,
	tbl_stock_detail.Qty,
	itminfo_tab.itm_type,
	tbl_stock_detail.PkDetailID,
	tbl_stock_detail.BatchID,
	itminfo_tab.itm_id,
	itminfo_tab.itm_name,
	itminfo_tab.qty_carton,
	(
		SELECT
			sum(placements.quantity) AS remValue
		FROM
			placements
		WHERE
			placements.stock_detail_id = tbl_stock_detail.PkDetailID
	) AS sumQty
	FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id";

        if (!empty($this->TranNo)) {
            $where[] = "tbl_stock_master.TranNo LIKE '%" . $this->TranNo . "%'";
        }
        if (!empty($this->batch_no)) {
            $where[] = "stock_batch.batch_no LIKE '%" . $this->batch_no . "%'";
        }
        if (!empty($this->TranRef)) {
            $where[] = "tbl_stock_master.TranRef LIKE '%" . $this->TranRef . "%'";
        }
        if (!empty($this->WHIDFrom)) {
            $where[] = "tbl_stock_master.WHIDFrom = '" . $this->WHIDFrom . "'";
        }
        if (!empty($this->item_id)) {
            $where[] = "stock_batch.item_id = '" . $this->item_id . "'";
        }
        if (!empty($this->manufacturer)) {
            $where[] = "tbl_stock_detail.manufacturer = '" . $this->manufacturer . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "DATE_FORMAT(tbl_stock_master.TranDate, '%Y-%m-%d') BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }

        $where[] = "tbl_stock_master.TranTypeID = $type";
        $where[] = "stock_batch.wh_id = $wh_id";
        $where[] = "tbl_stock_detail.temp = 0";

        if (is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }

        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * addStockSearch
     * @param type $type
     * @param type $wh_id
     * @return boolean
     * 
     * 
     */
    function addStockSearch($type, $wh_id) {
        //select query
        //gets
        //pk stock id
        //pk detail id
        //batch id
        //transaction date
        //item name
        //transaction num
        //batch num
        //batch expiry
        $strSql = "SELECT
	tbl_stock_master.PkStockID,
	tbl_stock_detail.PkDetailID,
	tbl_stock_detail.BatchID,
	tbl_stock_master.TranDate,
	itminfo_tab.itm_name,
	tbl_stock_master.TranNo,
	stock_batch.batch_no,
	stock_batch.batch_expiry,
	(
		tbl_stock_detail.Qty / itminfo_tab.qty_carton
	) AS receivedQty,
	sum(placements.quantity) AS allocated,
	ROUND(
		tbl_stock_detail.Qty / itminfo_tab.qty_carton
	) - sum(placements.quantity) AS unallocated
FROM
	tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
INNER JOIN placements ON stock_batch.batch_id = placements.stock_batch_id
WHERE
	tbl_stock_master.TranTypeID = $type
AND tbl_stock_master.WHIDTo = $wh_id
AND placements.quantity > 0
GROUP BY
	tbl_stock_detail.BatchID";

        if (!empty($this->TranNo)) {
            $where[] = "tbl_stock_master.TranNo = '" . $this->TranNo . "'";
        }
        if (!empty($this->batch_no)) {
            $where[] = "stock_batch.batch_no = '" . $this->batch_no . "'";
        }
        if (!empty($this->TranRef)) {
            $where[] = "tbl_stock_master.TranRef = '" . $this->TranRef . "'";
        }
        if (!empty($this->WHIDFrom)) {
            $where[] = "tbl_stock_master.WHIDFrom = '" . $this->WHIDFrom . "'";
        }
        if (!empty($this->item_id)) {
            $where[] = "stock_batch.item_id = '" . $this->item_id . "'";
        }
        if (!empty($this->manufacturer)) {
            $where[] = "tbl_stock_detail.manufacturer = '" . $this->manufacturer . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "tbl_stock_master.TranDate BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }

        $where[] = "tbl_stock_master.TranTypeID = $type";
        $where[] = "stock_batch.wh_id = $wh_id";
        $where[] = "tbl_stock_detail.temp = 0";

        if (is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }

        $strSql = $strSql . ' ORDER BY tbl_stock_master.TranNo DESC';

        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * DelStockSearch
     * @param type $type
     * @param type $wh_id
     * @return boolean
     * 
     * 
     */
    function DelStockSearch($type, $wh_id) {
        //select query
        //gets
        //transaction date
        //transaction number
        //transaction ref
        //warehouse name
        //batch num
        //batch expiry
        //qty
        //unit type
        //pk detail id
        //batch id
        //doses per unit
        //item id
        //sysuser name
        $strSql = "SELECT
        log_tbl_stock_master.TranDate,
		log_tbl_stock_master.PkStockID,
        log_tbl_stock_master.TranNo,
        log_tbl_stock_master.TranRef,
        tbl_warehouse.wh_name,
        log_stock_batch.batch_no,
        log_stock_batch.batch_expiry,
        log_tbl_stock_detail.Qty,
        tbl_itemunits.UnitType,
		log_tbl_stock_detail.PkDetailID,
		log_tbl_stock_detail.BatchID,
		itminfo_tab.doses_per_unit,
		itminfo_tab.itm_id,
		CONCAT(DATE_FORMAT(log_tbl_stock_master.deleted_on, '%d/%m/%Y %h:%i:%s %p')) AS DeletedOn,
		sysuser_tab.sysusr_name,
        itminfo_tab.itm_name,(select  sum(placement.qty) as remValue from placement where placement.StockIDetaild=log_tbl_stock_detail.PkDetailID and is_placed=1) as sumQty
        FROM
        log_tbl_stock_master
        INNER JOIN log_tbl_stock_detail ON log_tbl_stock_master.PkStockID = log_tbl_stock_detail.fkStockID
        INNER JOIN tbl_warehouse ON log_tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
        INNER JOIN log_stock_batch ON log_tbl_stock_detail.BatchID = log_stock_batch.batch_id
        INNER JOIN itminfo_tab ON log_stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN tbl_itemunits ON itminfo_tab.fkUnitID = tbl_itemunits.pkUnitID
		INNER JOIN sysuser_tab ON log_tbl_stock_master.deleted_by = sysuser_tab.UserID";

        if (!empty($this->TranNo)) {
            $where[] = "log_tbl_stock_master.TranNo = '" . $this->TranNo . "'";
        }
        if (!empty($this->batch_no)) {
            $where[] = "log_stock_batch.batch_no = '" . $this->batch_no . "'";
        }
        if (!empty($this->TranRef)) {
            $where[] = "log_tbl_stock_master.TranRef = '" . $this->TranRef . "'";
        }
        if (!empty($this->WHIDFrom)) {
            $where[] = "log_tbl_stock_master.WHIDFrom = '" . $this->WHIDFrom . "'";
        }
        if (!empty($this->item_id)) {
            $where[] = "log_stock_batch.item_id = '" . $this->item_id . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "log_tbl_stock_master.TranDate BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }

        $where[] = "log_tbl_stock_master.TranTypeID = $type";
        $where[] = "log_tbl_stock_detail.temp = 0";

        if (is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }

        $strSql = $strSql . ' ORDER BY log_tbl_stock_master.TranNo DESC';

        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * StockIssueSearch
     * @param type $type
     * @param type $wh_id
     * @param type $groupby
     * @return boolean
     * 
     * 
     */
    function StockIssueSearch($type, $wh_id, $groupby = '', $page_type = '') {

        if ($page_type == 'summary') {
            $detail_column = "SUM(tbl_stock_detail.Qty) as Qty";
        } else {
            $detail_column = "tbl_stock_detail.Qty";
        }
        //select query
        //gets
        //transaction date
        //pk stock id
        //transaction num
        //pk detail id
        //warehouse name
        //funding source
        //batch
        //batch expiry
        //qty
        //unit type
        //item name
        //carton qty
        $strSql = "SELECT
                        tbl_stock_master.TranDate,
                        tbl_stock_master.PkStockID,
                        tbl_stock_master.TranNo,
                        tbl_stock_master.TranRef,
                        tbl_stock_detail.PkDetailID,
                        tbl_warehouse.wh_name AS wh_name,

                        fundingSource.wh_name AS funding_source,
                        stock_batch.batch_no,
                        stock_batch.batch_expiry,
                        stock_batch.manufacturer,
                        tbl_stock_detail.BatchID,
                        $detail_column,
                        tbl_itemunits.UnitType,
                        itminfo_tab.itm_name,
                        IFNULL(stakeholder_item.quantity_per_pack, itminfo_tab.qty_carton) qty_carton,
                        itminfo_tab.field_color,
                        stakeholder.stkname,
                        stk_ofc.stkname as stk_office_name,
                                (
                                        SELECT
                                                sum(placements.quantity) AS remValue
                                        FROM
                                                placements
                                        WHERE
                                                placements.stock_detail_id = tbl_stock_detail.PkDetailID
                                        AND is_placed = 0
                                ) AS sumIssueQty,
                                tbl_stock_detail.IsReceived,
                        stk.stkname AS stakeholder_name,
                        tbl_locations.LocName as province,
                        tbl_stock_master.CreatedBy,
                        tbl_stock_master.CreatedOn,
                        stk_ofc.lvl
        FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
        INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
	LEFT JOIN tbl_warehouse AS fundingSource ON stock_batch.funding_source = fundingSource.wh_id
        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
       	LEFT JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
        LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
        LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
        left JOIN stakeholder AS stk_ofc ON tbl_warehouse.stkofficeid = stk_ofc.stkid 
        LEFT JOIN stakeholder AS stk ON tbl_warehouse.stkid = stk.stkid
        LEFT JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID  ";


        if (!empty($this->TranNo)) {
            $where[] = "tbl_stock_master.TranNo like '%" . $this->TranNo . "%'";
        }
        if (!empty($this->batch_no)) {
            $where[] = "stock_batch.batch_no like '%" . $this->batch_no . "%'";
        }
        if (!empty($this->TranRef)) {
            $where[] = "tbl_stock_master.TranRef like '%" . $this->TranRef . "%'";
        }
        if (!empty($this->WHIDTo)) {
            $where[] = "tbl_stock_master.WHIDTo = '" . $this->WHIDTo . "'";
        }
        if (!empty($this->province)) {
            $where[] = "tbl_warehouse.prov_id = '" . $this->province . "'";
        }
        if (!empty($this->stakeholder)) {
            $where[] = "tbl_warehouse.stkid = '" . $this->stakeholder . "'";
        }
        if (!empty($this->item_id)) {
            $where[] = "stock_batch.item_id = '" . $this->item_id . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "tbl_stock_master.TranDate BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }

        if (!empty($this->funding_source)) {
            $where[] = " stock_batch.funding_source = " . $this->funding_source . " ";
        }

        $where[] = "tbl_stock_master.TranTypeID = $type";
        $where[] = "stock_batch.wh_id = $wh_id";
        $where[] = "tbl_stock_detail.temp = 0";

        if (is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }
        $groupby = !empty($groupby) ? $groupby : ' ORDER BY tbl_stock_master.TranDate DESC, tbl_stock_master.TranNo DESC';
        $strSql = $strSql . $groupby;

        //echo $strSql;

        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * DelStockIssueSearch
     * @param type $type
     * @param type $wh_id
     * @return boolean
     * 
     * 
     */
    function DelStockIssueSearch($type, $wh_id) {
        //select query
        //gets
        //transaction date
        //pk stock id
        //transaction num
        //transaction ref
        //pk detail id
        //warehouse name
        //batch number
        //batch expiry
        //batch id
        //qty
        //item name
        //unit type
        $strSql = "SELECT
        log_tbl_stock_master.TranDate,
		log_tbl_stock_master.PkStockID,
        log_tbl_stock_master.TranNo,
        log_tbl_stock_master.TranRef,
		log_tbl_stock_detail.PkDetailID,
        tbl_warehouse.wh_name,
        log_stock_batch.batch_no,
        log_stock_batch.batch_expiry,
		log_tbl_stock_detail.BatchID,
        log_tbl_stock_detail.Qty,
        tbl_itemunits.UnitType,
        itminfo_tab.itm_name,
		itminfo_tab.doses_per_unit,
		CONCAT(DATE_FORMAT(log_tbl_stock_master.deleted_on, '%d/%m/%Y %h:%i:%s %p')) AS DeletedOn,
		sysuser_tab.sysusr_name,
		(select sum(placement.qty) as remValue from placement where placement.StockIDetaild=log_tbl_stock_detail.PkDetailID and is_placed=0) as sumIssueQty
        FROM
        log_tbl_stock_master
        INNER JOIN log_tbl_stock_detail ON log_tbl_stock_master.PkStockID = log_tbl_stock_detail.fkStockID
        INNER JOIN tbl_warehouse ON log_tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
        INNER JOIN log_stock_batch ON log_tbl_stock_detail.BatchID = log_stock_batch.batch_id
        INNER JOIN itminfo_tab ON log_stock_batch.item_id = itminfo_tab.itm_id
        INNER JOIN tbl_itemunits ON itminfo_tab.fkUnitID = tbl_itemunits.pkUnitID
		INNER JOIN sysuser_tab ON log_tbl_stock_master.deleted_by = sysuser_tab.UserID";

        if (!empty($this->TranNo)) {
            $where[] = "log_tbl_stock_master.TranNo like '" . $this->TranNo . "%'";
        }
        if (!empty($this->batch_no)) {
            $where[] = "log_stock_batch.batch_no like '" . $this->batch_no . "%'";
        }
        if (!empty($this->TranRef)) {
            $where[] = "log_tbl_stock_master.TranRef like '" . $this->TranRef . "%'";
        }
        if (!empty($this->WHIDTo)) {
            $where[] = "log_tbl_stock_master.WHIDTo = '" . $this->WHIDTo . "'";
        }
        if (!empty($this->item_id)) {
            $where[] = "log_stock_batch.item_id = '" . $this->item_id . "'";
        }
        if (!empty($this->fromDate) && !empty($this->toDate)) {
            $where[] = "log_tbl_stock_master.TranDate BETWEEN '" . $this->fromDate . "' AND '" . $this->toDate . "'";
        }

        $where[] = "log_tbl_stock_master.TranTypeID = $type";
        $where[] = "log_tbl_stock_detail.temp = 0";

        if (is_array($where)) {
            $strSql .= " WHERE " . implode(" AND ", $where);
        }
        $strSql = $strSql . ' GROUP BY log_stock_batch.batch_id, log_tbl_stock_master.WHIDTo ORDER BY log_tbl_stock_master.TranNo DESC';
        $rsSql = mysql_query($strSql) or trigger_error(mysql_error() . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * getLastID
     * @param type $from
     * @param type $to
     * @param type $tr_type
     * @return boolean
     * 
     * 
     */
    function getLastID($from, $to, $tr_type) {
        if ($tr_type == 1) {
            $str = "AND WHIDTo='" . $_SESSION['user_warehouse'] . "'";
        } else {
            $str = "AND WHIDFrom='" . $_SESSION['user_warehouse'] . "'";
        }
        $strSql = "SELECT MAX(trNo) as Maxtr from " . static::$table_name . " where TranDate between '" . $from . "' and '" . $to . "' AND TranTypeID = '" . $tr_type . "' $str";

        //echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error getLastID $strSql");
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_object($rsSql)) {
                return $row->Maxtr;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * getAdjLastID
     * @param type $from
     * @param type $to
     * @return boolean
     * 
     * 
     */
    function getAdjLastID($from, $to) {
        if (!empty($tr_type) && $tr_type == 1) {
            $str = "AND WHIDTo='" . $_SESSION['user_warehouse'] . "'";
        } else {
            $str = "AND WHIDFrom='" . $_SESSION['user_warehouse'] . "'";
        }
        $strSql = "SELECT MAX(trNo) as Maxtr from " . static::$table_name . " where TranDate between '" . $from . "' and '" . $to . "' AND TranTypeID != 1 AND TranTypeID != 2 $str";
        //echo $strSql;exit;
        $rsSql = mysql_query($strSql) or die("Error getLastID $strSql");
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_object($rsSql)) {
                return $row->Maxtr;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * GetWHStockByIssueNo
     * @return boolean
     * 
     * 
     */
    function GetWHStockByIssueNo() {
        //select query
        //gets
        //qty
        //batch num
        //item name
        //fk stock id
        //pk detail id
        $strSql = "SELECT
        tbl_stock_detail.Qty,
        stock_batch.batch_no,
        itminfo_tab.itm_name,
        tbl_stock_detail.fkStockID,
        tbl_stock_detail.PkDetailID
        FROM
        tbl_stock_master
        INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
        INNER JOIN itminfo_tab ON itminfo_tab.itm_id = stock_batch.item_id
        WHERE
        tbl_stock_master.TranNo = '" . $this->TranNo . "' AND
        tbl_stock_master.temp = 0 AND
        tbl_stock_master.WHIDTo = " . $this->WHIDTo . " AND
		(tbl_stock_detail.IsReceived is NULL or tbl_stock_detail.IsReceived = 0) AND tbl_stock_master.TranTypeID = 2";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetWHStockByIssueNo");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * getWhLevelByStockID
     * @param type $stock_id
     * @return boolean
     * 
     * 
     */
    function getWhLevelByStockID($stock_id) {
        //select query
        //gets
        //to warehose id
        //level
        //
        $strSql = "SELECT
			tbl_stock_master.WHIDTo,
			stakeholder.lvl
			FROM
			tbl_stock_master
			INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
			tbl_stock_master.PkStockID = $stock_id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error getWhLevelByStockID");
        if (mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_object($rsSql);
            return array(
                'uc_wh_id' => $row->WHIDTo,
                'level' => $row->lvl
            );
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * getDateAndItemfromStock
     * @param type $stock_id
     * @return boolean
     * 
     * 
     */
    function getDateAndItemfromStock($stock_id) {
        //select query
        //gets
        //transaction date
        //qty
        //item rec id
        //item id
        //
        $strSql = "SELECT
				tbl_stock_master.TranDate,
				tbl_stock_detail.Qty AS Qty,
				itminfo_tab.itmrec_id as itemrec_id,
				itminfo_tab.itm_id as item_id
				FROM
				tbl_stock_master
				INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
				INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
				INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
				WHERE
				tbl_stock_master.PkStockID = $stock_id AND tbl_stock_detail.IsReceived = 1
         ";
        //query result
        $rsSql = mysql_query($strSql) or die("Error getDateAndItemfromStock");
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_object($rsSql)) {
                $array[] = array(
                    'date' => $row->TranDate,
                    'item_id' => $row->item_id,
                    'itemrec_id' => $row->itemrec_id,
                    'qty' => $row->Qty
                );
            }
            return $array;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * getItemDetailfromStock
     * @param type $stock_id
     * @return boolean
     * 
     * 
     */
    function getItemDetailfromStock($stock_id) {
        //select query
        //gets
        //transaction date
        //qty
        //item rec id
        //item id
        $strSql = "SELECT
					tbl_stock_master.TranDate,
					tbl_stock_detail.Qty AS Qty,
					itminfo_tab.itmrec_id as itemrec_id,
					itminfo_tab.itm_id as item_id
					FROM
					tbl_stock_master
					INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
					INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
					INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
					WHERE	tbl_stock_master.PkStockID =  $stock_id";
        //query deatil
        $rsSql = mysql_query($strSql) or die("Error getItemDetailfromStock");
        if (mysql_num_rows($rsSql) > 0) {
            while ($row = mysql_fetch_object($rsSql)) {
                $array[] = array(
                    'date' => $row->TranDate,
                    'item_id' => $row->item_id,
                    'itemrec_id' => $row->itemrec_id,
                    'qty' => $row->Qty
                );
            }
            return $array;
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * updateMasterIssueDate
     * @param type $stock_id
     * @return boolean
     * 
     * 
     */
    function updateMasterIssueDate($stock_id) {
        //update query
        $strSql = "UPDATE
						tbl_stock_master 
					SET
						tbl_stock_master.TranDate = '" . $this->TranDate . "',
						tbl_stock_master.issued_by = '" . $this->issued_by . "',
						tbl_stock_master.TranRef = '" . $this->TranRef . "',
						tbl_stock_master.ReceivedRemarks = '" . $this->ReceivedRemarks . "'
					WHERE
						tbl_stock_master.PkStockID = '" . $stock_id . "' ";
        mysql_query($strSql);
        return true;
    }

    public function getPendingVouchers($dist_id) {
        //update query
        $strSql = "SELECT DISTINCT
	tbl_stock_master.TranNo,
	tbl_stock_master.PkStockID
FROM
	tbl_stock_master
INNER JOIN tbl_stock_detail ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
WHERE
tbl_stock_master.WHIDTo = $dist_id AND
tbl_stock_master.TranTypeID = 2 AND
tbl_stock_detail.IsReceived = 0";
        $rsSql = mysql_query($strSql) or die("Error getPendingVouchers");
        if (mysql_num_rows($rsSql) > 0) {
            $data = "";
            while ($row = mysql_fetch_assoc($rsSql)) {
                if ($_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == 'localhost')
                    $data .= "<a style='color:red' href='/clmis/application/im/new_receive_wh.php?issue_no=" . $row['TranNo'] . "&search=true'>" . $row['TranNo'] . "</a>, ";
                else
                    $data .= "<a style='color:red' href='/clmisapp/application/im/new_receive_wh.php?issue_no=" . $row['TranNo'] . "&search=true'>" . $row['TranNo'] . "</a>, ";
            }
            return $data;
        } else {
            return '';
        }
    }

    public function autoReceiveVoucher($stockid) {
        mysql_query("INSERT INTO tbl_stock_master
SELECT
	'',
	tbl_stock_master.TranDate,
	CONCAT('R',SUBSTRING(tbl_stock_master.TranNo,2)),
	1,
	tbl_stock_master.TranRef,
	tbl_stock_master.WHIDFrom,
	tbl_stock_master.WHIDTo,
	tbl_stock_master.CreatedBy,
	tbl_stock_master.CreatedOn,
	tbl_stock_master.ReceivedRemarks,
	tbl_stock_master.temp,
	tbl_stock_master.trNo,
	tbl_stock_master.LinkedTr,
	tbl_stock_master.issued_by,
	tbl_stock_master.is_copied,
	tbl_stock_master.shipment_id
FROM
	tbl_stock_master
WHERE
	tbl_stock_master.PkStockID = $stockid");
    }

    /**
     * Auto Receive Data
     *
     * @param type $stock_master_id
     * @return boolean
     */
    public function autoReceiveData($stock_id) {
        $objTransType = new clsTransTypes();
        $objStockDetail = new clsStockDetail();
        $objFiscalYear = new clsFiscalYear();
        $objStockBatch = new clsStockBatch();
        $objWhData = new clsWarehouseData();

        if (isset($stock_id) && !empty($stock_id)) {
            $arr_types = $objTransType->get_all();
            $array_types = array();
            foreach ($arr_types as $arrtype) {
                $array_types[$arrtype->trans_id] = $arrtype->trans_nature;
            }
            
            $type_id = 1;
            //find by stock id
            $stockDetail = $objStockDetail->find_by_stock_id($stock_id);

            if (mysql_num_rows($stockDetail) > 0) {
                $count = 1;
                while ($data = mysql_fetch_object($stockDetail)) {
                    
                    $user_warehouse = $data->WHIDTo;

                    if ($count == 1) {
                        $issued_by = $data->issued_by;
                        if (empty($issued_by)) {
                            $issued_by = 119;
                        }
                        //transaction type id
                        $this->TranTypeID = $type_id;
                        //transaction date
                        $this->TranDate = $data->TranDate;
                        //transaction reference
                        $this->TranRef = $data->TranNo;
                        //from warehouse
                        $this->WHIDFrom = $data->WHIDFrom;
                        //issued by
                        $this->issued_by = $issued_by;
                        //to warehouse
                        $this->WHIDTo = $data->WHIDTo;
                        //created by
                        $this->CreatedBy = $data->CreatedBy;
                        //created on
                        $this->CreatedOn = date("Y-m-d");
                        //Received Remarks 
                        $this->ReceivedRemarks = $data->ReceivedRemarks;
                        //temp
                        $this->temp = 0;
                        //LinkedTr 
                        $this->LinkedTr = 0;
                        //get get Fiscal Year
                        $fy_dates = $objFiscalYear->getFiscalYear();

                        //changing the from and to date , from fiscal year to only month
                        //$date_breakdown = explode('/', $data->TranDate);
//                        $year1 = $date_breakdown[2];
//                        $mon1 = $date_breakdown[1];
//                        $date1 = $date_breakdown[0];
//                        $issue_date_formatted = $year1 . '-' . $mon1 . '-' . $date1;
                        $issue_date_formatted = date('Y-m-d',strtotime($data->TranDate));
                        //echo '<pre>';print_r($fy_dates);exit;
                        //get Last ID
                        $last_id = $this->getLastID(date('Y-m-01', strtotime($issue_date_formatted)), date('Y-m-t', strtotime($issue_date_formatted)), $type_id);

                        if ($last_id == NULL) {
                            $last_id = 0;
                        }
                        //transaction number
                        $trans_no = "R" . date('ym') . str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
                        //set transaction number
                        //echo $trans_no;exit;
                        $this->TranNo = $trans_no;
                        $this->trNo = ($last_id + 1);
                        $fkStockID = $this->save();
                    }

                    $detail_id = $data->PkDetailID;

                    $objStockDetail->StockReceived($detail_id);
                    //Get Batch Detail
                    $stockBatch = $objStockDetail->GetBatchDetail($detail_id);

                    //quantity
                    $quantity = str_replace("-", "", $stockBatch->Qty);
                    //product id
                    $product_id = $stockBatch->item_id;
                    //batch number
                    $objStockBatch->batch_no = $stockBatch->batch_no;
                    //batch expiry
                    $objStockBatch->batch_expiry = $stockBatch->batch_expiry;
                    $objStockBatch->funding_source = $stockBatch->funding_source;
                    $objStockBatch->manufacturer = $stockBatch->manufacturer;
                    //quantity
                    $objStockBatch->Qty = $quantity;
                    //item id
                    $objStockBatch->item_id = $product_id;
                    //status
                    $objStockBatch->status = 'Stacked';
                    //unit price
                    $objStockBatch->unit_price = $stockBatch->unit_price;
                    //production date
                    $objStockBatch->production_date = $stockBatch->production_date;
                    //warehouse id
                    $objStockBatch->wh_id = $user_warehouse;
                    //            save stock batch
                    //            echo '<pre>';
                    //            print_r($stockBatch);
                    //            print_r($objStockBatch);
                    //            exit;
                    $objStockBatch->check_in_wh_id = $user_warehouse;
                    $batch_id1 = $objStockBatch->save();

                    //fk stock id
                    $objStockDetail->fkStockID = $fkStockID;
                    //batch id
                    $objStockDetail->BatchID = $batch_id1;
                    //fk unit id
                    $objStockDetail->fkUnitID = $data->fkUnitID;
                    //quantity
                    $objStockDetail->Qty = $array_types[$type_id] . $quantity;
                    //temp
                    $objStockDetail->temp = 0;
                    //is received
                    $objStockDetail->IsReceived = 1;
                    //adjustment type
                    $objStockDetail->adjustmentType = $type_id;
                    //save stock detail
                    $objStockDetail->save();

                    // Adjust Batch Quantity
                    $objStockBatch->adjustQtyByWh($batch_id1, $user_warehouse);
                    //auto Running LEFO Batch
                    $objStockBatch->autoRunningLEFOBatch($product_id, $user_warehouse);
                    $count++;
                }
                //Save Data in WH data table (Need modification)
                $objWhData->addReport($fkStockID, 1, 'wh');
            }
            return true;
        }
        return false;
    }

}
?>