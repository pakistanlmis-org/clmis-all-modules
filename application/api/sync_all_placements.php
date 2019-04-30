<?php

/**
 * sync_all_placements
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

// Include Database Connection File
include_once("DBCon.php");          
// Example Call for 1st time: 
// http://localhost/clmis/ws/sync_transactions.php?td=2014-02-19&tn=0006&tt=1&tr=000056&wf=1&wt=2&cb=99&co=2014-02-19&rr=remakrs&bn=b00001&be=2015-01-01&itm=12&qty=1000
// Example Call for when we have master Id: 
// http://localhost/clmis/ws/sync_transactions.php?bn=b00001&be=2015-01-01&itm=12&qty=1000&mId=12

//Getting form data
//Getting
$qty = !empty($_REQUEST['qty']) ? $_REQUEST['qty'] : '';
//Getting qty 
$pid = !empty($_REQUEST['pid']) ? $_REQUEST['pid'] : '';
//Getting bid
$bid = !empty($_REQUEST['bid']) ? $_REQUEST['bid'] : '';
//Getting did
$did = !empty($_REQUEST['did']) ? $_REQUEST['did'] : '';
//Getting cb 
$cb = !empty($_REQUEST['cb']) ? $_REQUEST['cb'] : '';
//Getting cd
$cd = !empty($_REQUEST['cd']) ? $_REQUEST['cd'] : '';
//Getting tt
$tt = !empty($_REQUEST['tt']) ? $_REQUEST['tt'] : '';

//for sync_all_placements
$detailqry = "SELECT
                    PkDetailID,
                    TranDate
                    FROM
                    tbl_stock_detail
                    INNER JOIN tbl_stock_master ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                    WHERE
                    tbl_stock_detail.Qty > 0 AND
                    tbl_stock_detail.BatchID = '" . $bid . "' LIMIT 1;";



$detailExist = mysql_fetch_array(mysql_query($detailqry));

$PkDetailID = $detailExist['PkDetailID'];

// Transfer
$qry = "INSERT INTO placements
				SET
                                placements.quantity = '" . (-1 * $qty) . "',
                                placements.placement_location_id = 4432,
                                placements.stock_batch_id = '" . $bid . "',
                                placements.stock_detail_id = '" . $PkDetailID . "',
                                placements.placement_transaction_type_id =90 ,
                                placements.created_by = '" . $cb . "',
                                placements.created_date = '" . $detailExist['TranDate'] . "',
                                placements.is_placed=0;";
mysql_query($qry);

$transferId = mysql_insert_id();

// Place
$qry1 = "INSERT INTO placements
				SET
                                placements.quantity = '" . $qty . "',
                                placements.placement_location_id = '" . $pid . "',
                                placements.stock_batch_id = '" . $bid . "',
                                placements.stock_detail_id = '" . $PkDetailID . "',
                                placements.placement_transaction_type_id = 89,
                                placements.created_by = '" . $cb . "',
                                placements.created_date = '" . $detailExist['TranDate'] . "',
                                placements.is_placed=0;";


mysql_query($qry1);
$placementId = mysql_insert_id();
$arr = array('Transferid' => $transferId, 'placementid' => $placementId);

$arr1[] = $arr;
//encode in json
print(json_encode($arr1));
?>