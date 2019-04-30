<?php

/**
 * get-issue-voucher-list
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including Configuration file
include("../../application/includes/classes/Configuration.inc.php");
//Including db file
include(APP_PATH . "includes/classes/db.php");
//Including auth file
include('auth.php');
//Checking wh_id
if (isset($_GET['wh_id'])) {
//Getting wh_id	
    $wh_id = $_GET['wh_id'];
//Query for getting issue voucher list
//Gets
//transaction_number
//stc_master_pkid
//transaction_date
//warehouse_name
//from_warehouse_id
  
    $query = "SELECT DISTINCT
	A.TranNo AS transaction_number,
	A.PkStockID AS stc_master_pkid,
	A.TranDate AS transaction_date,
	A.warehouse_name,
	A.to_warehouse_id,
	A.from_warehouse_id
        FROM
	(
		SELECT DISTINCT
			tbl_stock_master.TranNo,
			tbl_stock_master.PkStockID,
			tbl_stock_master.TranDate,
			tbl_warehouse.wh_name AS warehouse_name,
			ABS(
				GetPicked (
					tbl_stock_detail.PkDetailID
				)
			) AS Picked,
			ABS(tbl_stock_detail.Qty) AS Qty,
			tbl_stock_master.WHIDTo AS to_warehouse_id,
			tbl_stock_master.WHIDFrom AS from_warehouse_id
		FROM
			tbl_stock_master
		INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
		INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
		WHERE
			tbl_stock_master.TranTypeID = 2
		AND tbl_stock_master.WHIDFrom = $wh_id	
                AND  tbl_stock_master.TranDate >= '2016-01-01'		
		ORDER BY
			tbl_stock_master.TranDate DESC
	) A
WHERE
	A.Qty > A.Picked";
    
//Query result
    $rs = mysql_query($query) or die(print mysql_error());
//For result
    $rows = array();
//Populate array
    while ($r = mysql_fetch_assoc($rs)) {
        $rows[] = $r;
    }
//Encode in json
    print json_encode($rows);
} else {
    //Display message
    print json_encode('Please provide wh_id like ?wh_id=1');
}
//Just for
// example: http://localhost/clmisapp/application/api/get-issue-voucher-list.php?wh_id=123&auth=a7bd3fa3defff6c1e3c66a2b06d578c1
?>