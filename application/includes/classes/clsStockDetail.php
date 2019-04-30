<?php

/**
 * clsStockDetail
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
class clsStockDetail {
    //table name
    protected static $table_name = "tbl_stock_detail";
    //db fields
    protected static $db_fields = array('fkStockID', 'BatchID', 'fkUnitID', 'Qty', 'temp', 'comments', 'IsReceived', 'adjustmentType');
    //pk detail id
    public $PkDetailID;
    //fk stock id
    public $fkStockID;
    //batch id
    public $BatchID;
    //fk unit id
    public $fkUnitID;
    //qty
    public $Qty;
    //temp
    public $temp;
    //vvm stage
    public $vvmstage;
    //is received
    public $IsReceived;
    //adjustment type
    public $adjustmentType;

    // Common Database Methods
    /**
     * find_all
     * @return type
     */
    public static
            function find_all() {
        return static::find_by_sql("SELECT * FROM " . static::$table_name);
    }

    /**
     * find_by_id
     * @param type $id
     * @return type
     */
    public static
            function find_by_id($id = 0) {
        $result_array = static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE PkDetailID={$id} LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    /**
     * find_by_stock_id
     * @param type $id
     * @return boolean
     */
    public
            function find_by_stock_id($id = 0) {
        //select query
        //gets
        //pk detail id
        //fk stock id
        //batch id
        //fk unit id
        //qty
        //vvm stage
        //is received
        //adjustment type
        //from warehouse id
        //issued by
        $strSql = "SELECT
            tbl_stock_detail.PkDetailID,
            tbl_stock_detail.fkStockID,
            tbl_stock_detail.BatchID,
            tbl_stock_detail.fkUnitID,
            tbl_stock_detail.Qty,
            tbl_stock_detail.temp,
            tbl_stock_detail.vvm_stage,
            tbl_stock_detail.IsReceived,
            tbl_stock_detail.adjustmentType,
            tbl_stock_master.WHIDTo,
            tbl_stock_master.TranDate,
            tbl_stock_master.TranRef,
            tbl_stock_master.TranNo,
            tbl_stock_master.CreatedBy,
            tbl_stock_master.WHIDFrom,
            tbl_stock_master.issued_by,
            tbl_stock_master.ReceivedRemarks
        FROM
        tbl_stock_detail
        INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
        WHERE
        tbl_stock_master.PkStockID = $id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error: find_by_stock_id: " . mysql_error());
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * find_by_detail_id
     * @param type $id
     * @return boolean
     */
    public
            function find_by_detail_id($id = 0) {
        $strSql = "SELECT
        tbl_stock_detail.PkDetailID,
        tbl_stock_detail.fkStockID,
        tbl_stock_detail.BatchID,
        tbl_stock_detail.fkUnitID,
        tbl_stock_detail.Qty,
        tbl_stock_detail.temp,
        tbl_stock_detail.vvm_stage,
        tbl_stock_detail.IsReceived,
        tbl_stock_detail.adjustmentType,
        tbl_stock_master.WHIDTo,
		tbl_stock_master.WHIDFrom
        FROM
        tbl_stock_detail
        INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
        WHERE
        tbl_stock_detail.PkDetailID = $id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error find_by_detail_id" . $strSql);
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

    /**
     * StockReceived
     * @param type $id
     * @return boolean
     */
    public function StockReceived($id) {
        $strSql = "Update " . static::$table_name . " set isReceived=1 where PkDetailID=$id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error StockReceived");
        if (mysql_affected_rows() > 0) {
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * updateDetail
     * @param type $oldid
     * @param type $new_batch_id
     * @param type $stk_detail_id
     * @return boolean
     */
    public function updateDetail($oldid, $new_batch_id, $stk_detail_id) {
        mysql_query("Update " . static::$table_name . " set BatchID=$new_batch_id where PkDetailID = ".$stk_detail_id." ") or die("Error updateDetail");
        if (mysql_affected_rows() > 0) {
            $row = mysql_fetch_array(mysql_query("SELECT
                    SUM(tbl_stock_detail.Qty) as quantity
                    FROM
                    tbl_stock_detail
                    INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                    WHERE
                    stock_batch.wh_id = " . $_SESSION['user_warehouse'] . " AND
                    tbl_stock_detail.BatchID = $new_batch_id"));
            mysql_query("Update stock_batch set Qty=" . $row['quantity'] . " where batch_id = $new_batch_id");
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * deleteReceive
     * @param type $id
     * @return boolean
     */
    public function deleteReceive($id) {
         $result = $this->getQtyById($id);
        $qty = $result['Qty'];
        $batch_id = $result['BatchID'];
        $stock_id = $result['fkStockID'];

        $objWarehouseData = new clsWarehouseData();
        $objWarehouseData->detail_id = $id;
        $params = $objWarehouseData->getStockReportParams();
         $bQty = $this->getBatchQtyById($batch_id);

        $objStockBatch = new clsStockBatch();
        if ($bQty == $qty) {

            $objStockBatch->adjustQty($batch_id, "Qty-$qty");
            $objStockBatch->changeWh($batch_id);
        } else if ($bQty > $qty) {
            $objStockBatch->adjustQty($batch_id, "Qty-$qty");
        }

        $this->PkDetailID = $id;
        $del = $this->delete($id);

        $objStockMaster = new clsStockMaster();
        $objStockMaster->PkStockID = $stock_id;
        $objStockMaster->delete();

        $objWarehouseData->report_month = $params['month'];
        $objWarehouseData->report_year = $params['year'];
        $objWarehouseData->item_id = $params['item_id'];
        $objWarehouseData->wh_id = $params['wh_id'];
        $objWarehouseData->created_by = $params['created_by'];
        $objWarehouseData->itmrec_id = $params['itmrec_id'];
        $objWarehouseData->adjustStockReport();

		// Delete Placement Entries
		$this->deletePlacement($id);

        if ($del) {
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * editReceive
     * @param type $id
     * @param type $uQty
     * @return boolean
     */
    public function editReceive($id, $uQty) {
        $result = $this->getQtyById($id);
        $dQty = $result['Qty'];
        $batch_id = $result['BatchID'];
        $bQty = $this->getBatchQtyById($batch_id);

        $objStockBatch = new clsStockBatch();
        if ($uQty == $dQty) {
            return true;
        } else if ($uQty < $dQty) {
            $resultQty = $dQty - $uQty;
            $objStockBatch->adjustQty($batch_id, "Qty-$resultQty");
            return $this->adjustQty($id, $uQty);
        } else if ($uQty > $dQty) {
            $resultQty = $uQty - $dQty;
            $objStockBatch->adjustQty($batch_id, "Qty+$resultQty");
            return $this->adjustQty($id, $uQty);
        }
    }

    /**
     * deleteIssue
     * @param type $id
     * @return boolean
     */
    public function deleteIssue($id) {
        $result = $this->getQtyById($id);
        $qty = abs($result['Qty']);
        $batch_id = $result['BatchID'];
        $stock_id = $result['fkStockID'];
        $master_stock_id = $result['fkStockID'];
        $item_id = $result['item_id'];
        //echo '<pre>';print_r($result);exit;
        $objWarehouseData = new clsWarehouseData();
        $objWarehouseData->detail_id = $id;
        $params = $objWarehouseData->getStockReportParams();

        $objStockBatch = new clsStockBatch();
        $objStockBatch->adjustQty($batch_id, "Qty+$qty");
        $objStockBatch->changeStatus($batch_id, 'Running');

        $this->PkDetailID = $id;
        $del = $this->delete($id);

        $objStockMaster = new clsStockMaster();
        $objStockMaster->PkStockID = $stock_id;
       	$objStockMaster->delete();

        $objWarehouseData->report_month = $params['month'];
        $objWarehouseData->report_year = $params['year'];
        $objWarehouseData->item_id = $params['item_id'];
        $objWarehouseData->wh_id = $params['wh_id'];
        $objWarehouseData->created_by = $params['created_by'];
        $objWarehouseData->itmrec_id = $params['itmrec_id'];
        $objWarehouseData->adjustStockReport();

        //update status of CLR from issued to Approved
        // remove the stock_id from clr master table
        $this->update_clr_status($id,$master_stock_id,$item_id);


        // Delete Placement Entries
        $this->deletePlacement($id);

        if ($del) {
            return true;
        } else {
            return FALSE;
        }
    }


	public function update_clr_status($detailId,$stock_master_id,$item_id){
            
            $qry = "SELECT pk_master_id from  clr_details
				WHERE
					stock_master_id = " . $stock_master_id ;
            //echo $qry;
            $res = mysql_query($qry);
            $row  = mysql_fetch_assoc($res);
            $master_id = $row['pk_master_id'];
            $itm_id = $item_id;
            
            $qry = "Update  clr_details SET approval_status='Approved'  WHERE pk_master_id = " . $master_id ." AND itm_id = ".$itm_id;
            $del = mysql_query($qry);
            //echo $qry;
            
            $qry = "Update  clr_master SET approval_status='Approved' WHERE pk_id = " . $master_id;
            $del = mysql_query($qry);
            //echo $qry;exit;
            if ($del) {
                    return true;
                } else {
                    return FALSE;
                }
	}


    /**
     * Delete Placements
     * @param type $StockDetailId
     * @param type Stock Detail Id for which Placement is to be deleted
     * @return boolean
     */
	public function deletePlacement($detailId){
		$qry = "DELETE
				FROM
					placements
				WHERE
					placements.stock_detail_id = " . $detailId;
		$del = mysql_query($qry);
		if ($del) {
            return true;
        } else {
            return FALSE;
        }
	}
    /**
     * editIssue
     * @param type $id
     * @param type $uQty
     * @return boolean
     */
    public function editIssue($id, $uQty) {
        $result = $this->getQtyById($id);
        $dQty = abs($result['Qty']);
        $batch_id = $result['BatchID'];
        $bQty = $this->getBatchQtyById($batch_id);

        $objStockBatch = new clsStockBatch();
        if ($uQty == $dQty) {
            return true;
        } else if ($uQty < $dQty) {
            $resultQty = $dQty - $uQty;
            $objStockBatch->adjustQty($batch_id, "Qty+$resultQty");
            return $this->adjustQty($id, "-" . $uQty);
        } else if ($uQty > $dQty) {
            $resultQty = $uQty - $dQty;
            $objStockBatch->adjustQty($batch_id, "Qty-$resultQty");
            return $this->adjustQty($id, "-" . $uQty);
        }
    }

    /**
     * adjustQty
     * @param type $id
     * @param type $qty
     * @return boolean
     */
    function adjustQty($id, $qty) {
        $strSql = "UPDATE " . static::$table_name . " SET Qty=" . $qty;
        $strSql .= " WHERE PkDetailID='" . $id . "'";
        $rsSql = mysql_query($strSql) or die("Error adjustQty");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * getQtyById
     * @param type $id
     * @return boolean
     */
    public function getQtyById($id) {
        $strSql = "SELECT
                tbl_stock_detail.Qty,
                tbl_stock_detail.BatchID,
                tbl_stock_detail.fkStockID,
                stock_batch.item_id
                FROM
                " . static::$table_name . "
                INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                where PkDetailID=$id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error getQtyById");
        if (!empty($rsSql) && mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_object($rsSql);
            return array(
                'Qty' => $row->Qty,
                'BatchID' => $row->BatchID,
                'fkStockID' => $row->fkStockID,
                'item_id' => $row->item_id
            );
        } else {
            return FALSE;
        }
    }

    /**
     * getBatchQtyById
     * @param type $id
     * @return boolean
     */
    public function getBatchQtyById($id) {
       $strSql = "Select Qty from stock_batch where batch_id=$id";
       //query result
        $rsSql = mysql_query($strSql) or die("Error getBatchQtyById");
        if (!empty($rsSql) && mysql_num_rows($rsSql) > 0) {
            $row = mysql_fetch_object($rsSql);
            return $row->Qty;
        } else {
            return FALSE;
        }
    }

    /**
     * GetBatchDetail
     * @param type $id
     * @return boolean
     */
    public function GetBatchDetail($id) {
        $strSql = "     SELECT
                            stock_batch.batch_no,
                            stock_batch.batch_expiry,
                            stock_batch.item_id,
                            stock_batch.unit_price,
                            stock_batch.production_date,
                            stock_batch.vvm_type,
                            tbl_stock_detail.Qty,
                            stock_batch.funding_source,
                            stock_batch.manufacturer
			FROM
                            tbl_stock_detail
			INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
			WHERE
                            tbl_stock_detail.PkDetailID = $id";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetBatchDetail");
        if (mysql_num_rows($rsSql) > 0) {
            return mysql_fetch_object($rsSql);
        } else {
            return FALSE;
        }
    }

    /**
     * find_by_sql
     * @param type $sql
     * @return type
     */
    public static
            function find_by_sql($sql = "") {
        $result_set = mysql_query($sql);
        $object_array = array();
        while ($row = mysql_fetch_array($result_set)) {
            $object_array[] = static::instantiate($row);
        }
        return $object_array;
    }

    /**
     * count_all
     * @global type $database
     * @return type
     */
    public static
            function count_all() {
        global $database;
        $sql = "SELECT COUNT(*) FROM " . static::$table_name;
        //query result
        $result_set = $database->query($sql);
        $row = $database->fetch_array($result_set);
        return array_shift($row);
    }

    /**
     * instantiate
     * @param type $record
     * @return \self
     */
    private static
            function instantiate($record) {
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
    private
            function has_attribute($attribute) {
        // We don't care about the value, we just want to know if the key exists
        // Will return true or false
        return array_key_exists($attribute, $this->attributes());
    }

    /**
     * attributes
     * @return type
     */
    protected
            function attributes() {
        // return an array of attribute names and their values
        $attributes = array();
        foreach (static::$db_fields as $field) {
            if (property_exists($this, $field)) {
                if (!empty($this->$field) || $this->$field == 0) {
                    $attributes[$field] = $this->$field;
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
    protected
            function sanitized_attributes() {
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
     * save
     * @return type
     */
    public
            function save() {
        // A new record won't have an id yet.
        //echo isset($this->PkDetailID) ? 'UPD.' :'Ins.';
        return isset($this->PkDetailID) ? $this->update() : $this->create();
    }

    /**
     * create
     * @global type $database
     * @return boolean
     */
    public
            function create() {
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
    public
            function update() {
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
        $sql .= " WHERE PkDetailID=" . $database->escape_value($this->PkDetailID);
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    /**
     * updateTemp
     * @param type $id
     * @return boolean
     */
    function updateTemp($id) {
         $strSql = "Update tbl_stock_detail set temp=0 where fkStockID=$id";
        $rsSql = mysql_query($strSql) or die("Error updateTemp");
        if (mysql_affected_rows() > 0) {
             $strSql2 = "SELECT DISTINCT
                                itminfo_tab.itm_id,
                                tbl_stock_master.WHIDTo
                        FROM
                                tbl_stock_detail
                        INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                        INNER JOIN tbl_stock_master ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                        WHERE
                                tbl_stock_detail.fkStockID = $id";
             //query result
            $rsSql2 = mysql_query($strSql2);
            if (!empty($rsSql2) && mysql_num_rows($rsSql2) > 0) {
                while ($row = mysql_fetch_object($rsSql2)) {
                    $objStockBatch = new clsStockBatch();
                    $objStockBatch->autoRunningLEFOBatch($row->itm_id, $row->WHIDTo);
                }
            }
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * updatefk
     * @global type $database
     * @return type
     */
    public
            function updatefk() {
        global $database;
        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= "temp = 0";
        $sql .= " WHERE fkStockID=" . $database->escape_value($this->fkStockID);
        $database->query($sql);
        return ($database->affected_rows() == 1) ? true : false;
    }

    /**
     * delete
     * @global type $database
     * @return type
     */
    public
            function delete() {
        global $database;
        // Don't forget your SQL syntax and good habits:
        // - DELETE FROM table WHERE condition LIMIT 1
        // - escape all values to prevent SQL injection
        // - use LIMIT 1
        $sql = "DELETE FROM " . static::$table_name;
        $sql .= " WHERE PkDetailID=" . $database->escape_value($this->PkDetailID);
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

?>