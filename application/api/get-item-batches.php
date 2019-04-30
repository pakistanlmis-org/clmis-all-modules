<?php

/**
 * get-item-batches
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
//Getting wh_id
if(isset($_GET['wh_id'])){
$wh_id=$_GET['wh_id'];

//for getting item batches
$query="SELECT
stock_batch.batch_id AS pkid,
stock_batch.batch_no AS number,
stock_batch.batch_expiry AS expiryDate,
stock_batch.Qty AS quantity,
stock_batch.`status`,
stock_batch.item_id as itemPackSize,
stock_batch.item_id as StakeholderItemPackSizeID
FROM
stock_batch
WHERE
stock_batch.wh_id = $wh_id AND stock_batch.batch_expiry >= CURDATE()";
//Query result
$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
print json_encode($rows);
}
else{
	print json_encode("please provide wh_id");
}
// example: http://localhost/lmis/ws/locations.php?ID=4
?>