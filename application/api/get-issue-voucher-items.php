<?php
/**
 * get-issue-voucher-items
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including Configuration files
include("../../application/includes/classes/Configuration.inc.php");
//Including db files
include(APP_PATH."includes/classes/db.php");
//Including auth files
include('auth.php');
//Checking voucher_id
if(isset($_GET['voucher_id']))
{
//Getting voucher_id	
$voucher=$_GET['voucher_id'];

$wh_id=$_GET['wh_id'];

//Query for issue voucher
//Gets
//transaction_number
//quantity
//pk_id
//stc_master_pkid
//number
//batch_id
//expiry_date
//itemID
//quantity_per_pack
//place_quantity
$query = "SELECT
            tbl_stock_master.TranDate,
            ABS(tbl_stock_detail.Qty) AS quantity,
            (tbl_stock_detail.Qty / itminfo_tab.qty_carton) AS cartonQty,
            itminfo_tab.qty_carton as quantity_per_pack,
            itminfo_tab.itm_type,
            stock_batch.batch_no as number,
            stock_batch.batch_id as batch_id,
            stock_batch.unit_price,
            stock_batch.batch_expiry as expiry_date,
            itminfo_tab.itm_name as item_name,
            itminfo_tab.itm_id as itemID,
            tbl_warehouse.wh_name,
            tbl_stock_master.PkStockID as stc_master_pkid,
            tbl_stock_master.TranNo as transaction_number,
            tbl_stock_master.TranRef,
            tbl_stock_detail.PkDetailID as  pk_id,
            ABS(GetPicked(tbl_stock_detail.PkDetailID)) AS place_quantity
            FROM
                    tbl_stock_master
                    INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                    INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                    INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                    INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
            WHERE
                    tbl_stock_detail.temp = 0 AND
                    tbl_stock_master.WHIDFrom = '" . $wh_id . "' AND
                    tbl_stock_master.TranTypeID = 2 AND
                    tbl_stock_master.PkStockID = " . $voucher . "";


//Query results
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
        //Display message
	print json_encode('Please provide voucher_id like ?voucher_id=1');
}
//just for
// example: http://localhost/clmisapp/application/api/get-issue-voucher-items.php?voucher_id=8149&wh_id=123&auth=a7bd3fa3defff6c1e3c66a2b06d578c1
?>