<?php

/**
 * sync_placements
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
// Example Call for 1st time: http://localhost/clmis/ws/sync_transactions.php?td=2014-02-19&tn=0006&tt=1&tr=000056&wf=1&wt=2&cb=99&co=2014-02-19&rr=remakrs&bn=b00001&be=2015-01-01&itm=12&qty=1000
// Example Call for when we have master Id: http://localhost/clmis/ws/sync_transactions.php?bn=b00001&be=2015-01-01&itm=12&qty=1000&mId=12

$qty = !empty($_REQUEST['qty']) ? $_REQUEST['qty'] : '';
$pid = !empty($_REQUEST['pid']) ? $_REQUEST['pid'] : '';
$bid = !empty($_REQUEST['bid']) ? $_REQUEST['bid'] : '';
$did = !empty($_REQUEST['did']) ? $_REQUEST['did'] : '';
$cb = !empty($_REQUEST['cb']) ? $_REQUEST['cb'] : '';
$cd = !empty($_REQUEST['cd']) ? $_REQUEST['cd'] : '';
$tt = !empty($_REQUEST['tt']) ? $_REQUEST['tt'] : '';

//for sync_placements
$qry = "INSERT INTO placements
				SET
                                placements.quantity = '" . $qty . "',
                                placements.placement_location_id = '" . $pid . "',
                                placements.stock_batch_id = '" . $bid . "',
                                placements.stock_detail_id = '" . $did . "',
                                placements.placement_transaction_type_id = '" . $tt . "',
                                placements.created_by = '" . $cb . "',
                                placements.created_date = '" . $cd . "',
                                placements.is_placed=0;";

mysql_query($qry);
$arr = array('master_id' => $masterPKId, 'detail_id' => $detailPKId);

$arr1[] = $arr;
print(json_encode($arr1));
?>