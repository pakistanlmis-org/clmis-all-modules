<?php
/**
 * search-batch-locations
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including files
include("../../application/includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include('auth.php');
//Getting batch_id
if(isset($_GET['batch_id']) && ($_GET['wh_id']))
{
//Getting wh_id
$wh_id=$_GET['wh_id'];
$batch=$_GET['batch_id'];
//Query for for batch locations
$query="SELECT
stock_batch.batch_expiry AS Expiry,
stock_batch.batch_no AS batchNo,
stock_batch.item_id AS itemID,
stock_batch.batch_id AS batchID,
itminfo_tab.itm_name as item_name,
placements.stock_detail_id as DetailId,
placements.placement_location_id,
placement_config.location_name as LocationName,
placements.pk_id as PlacementID,
abs(placements.quantity) as Qty,
itminfo_tab.qty_carton AS quantity_per_pack

FROM
stock_batch
INNER JOIN placements ON stock_batch.batch_id = placements.stock_batch_id
INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
WHERE
placements.stock_batch_id = $batch AND
placement_config.warehouse_id = $wh_id
GROUP BY
placements.stock_batch_id,
placement_config.pk_id";
//Query results
$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
//Populate array 
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
print json_encode($rows);
}
else{
        //message
	print json_encode('Please provide wh_id,batch_id like ?wh_id=1&batch_id=2');
}
//just for
// example: http://localhost/lmis/ws/locations.php?ID=4
?>