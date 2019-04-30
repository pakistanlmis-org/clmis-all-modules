<?php

/**
 * get-receive-voucher-items
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Including required files
include("../../application/includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include('auth.php');
//Checking voucher_id
if(isset($_GET['voucher_id']))
{
//Getting voucher_id	
$voucher=$_GET['voucher_id'];
$wh_id = $_GET['wh_id'];


$query = "SELECT 
    
                A.TranNo AS transaction_number,
                A.Qty AS quantity,
                A.PkDetailID AS pk_id,
                A.PkStockID AS stc_master_pkid,
                A.BatchID as batch_id,
                A.batch_no AS number,
                A.batch_expiry AS expiry_date,
                A.itm_name as  item_name,
                A.itm_id AS itemID,
                A.qty_carton AS quantity_per_pack,
                IFNULL(B.placedQty, 0)  AS place_quantity
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
				AND tbl_stock_master.WHIDTo = $wh_id AND tbl_stock_master.PkStockID = $voucher
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


//Query result.
$rs = mysql_query($query) or die(print mysql_error());
//for query result
$rows = array();
//Populate array
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
//encode in json
print json_encode($rows);
}
else{
	print json_encode('Please provide voucher_id like ?voucher_id=1');
}
//just for 
// example: http://192.168.1.34:8080/clmisapp/application/api/get-receive-voucher-items.php?voucher_id=8053&auth=a7bd3fa3defff6c1e3c66a2b06d578c1
?>