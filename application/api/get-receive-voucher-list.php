<?php

/**
 * get-receive-voucher-list
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

//Query for receive voucher list
//Gets
//transaction_number
//stc_master_pkid
//transaction_date
//to_warehouse_id
//from_warehouse_id
//
    $query = "SELECT DISTINCT    
			A.PkStockID as stc_master_pkid,			
			A.TranDate as transaction_date,			
			A.TranNo as transaction_number,
                        A.warehouse_name,
                        A.to_warehouse_id,
                        A.from_warehouse_id			
		FROM
			(
				SELECT
					tbl_stock_master.PkStockID,
					tbl_stock_detail.PkDetailID,
					tbl_stock_detail.BatchID,
					tbl_stock_master.TranDate,
					itminfo_tab.itm_name,
					itminfo_tab.qty_carton,
					itminfo_tab.itm_id,
					itminfo_tab.itm_type,
					tbl_stock_master.TranNo,
					stock_batch.batch_no,
					stock_batch.batch_expiry,
                                        tbl_warehouse.wh_name AS warehouse_name,
                                       tbl_stock_master.WHIDTo AS to_warehouse_id,
                                       tbl_stock_master.WHIDFrom AS from_warehouse_id,
					SUM(tbl_stock_detail.Qty) AS Qty
				FROM
					tbl_stock_master
                                INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDFrom = tbl_warehouse.wh_id
				INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID                                
				INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
				INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
				INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
				WHERE
					tbl_trans_type.trans_nature = '+'
				AND tbl_stock_master.WHIDTo = $wh_id
				GROUP BY
					tbl_stock_detail.BatchID
			) A
		LEFT JOIN (
			SELECT
				tbl_stock_master.PkStockID,
				tbl_stock_master.TranNo,
				SUM(placements.quantity) AS placedQty,
				tbl_stock_detail.BatchID
			FROM
				tbl_stock_master
			INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
			INNER JOIN placements ON tbl_stock_detail.PkDetailID = placements.stock_detail_id
			INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
			WHERE
				tbl_trans_type.trans_nature = '+'
			AND tbl_stock_master.WHIDTo = $wh_id
			AND placements.placement_transaction_type_id IN (89, 90)
			GROUP BY
				tbl_stock_detail.BatchID
		) B ON A.BatchID = B.BatchID
		WHERE
			(A.Qty - IFNULL(B.placedQty, 0)) > 0";

//Query result
    $rs = mysql_query($query) or die(print mysql_error());
//for result
    $rows = array();
//Populate array
    while ($r = mysql_fetch_assoc($rs)) {
        $rows[] = $r;
    }
//encode in json
    print json_encode($rows);
} else {
    print json_encode('Please provide wh_id like ?wh_id=1');
}
//just for example
// example: http://localhost/clmisapp/application/api/get-receive-voucher-list.php?wh_id=123&auth=a7bd3fa3defff6c1e3c66a2b06d578c1
?>