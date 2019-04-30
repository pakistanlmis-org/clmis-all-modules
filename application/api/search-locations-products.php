<?php
/**
 * search-locations-products
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

if(isset($_GET['p_loc_id']) && ($_GET['wh_id']))
{
//Getting wh_id	
$wh_id=$_GET['wh_id'];
//Getting p_loc_id
$loc_id=$_GET['p_loc_id'];
//Query for search locations products
$query="SELECT
	stock_batch.batch_expiry AS expiry,
	placements.stock_batch_id AS batchID,
	stock_batch.batch_no AS batchNo,
	stock_batch.item_id AS itemID,
	itminfo_tab.itm_name AS ItemName,
	itminfo_tab.qty_carton AS qty_per_pack,
	placements.stock_detail_id AS DetailID,
	placement_config.location_name AS LocationName,
	placement_config.pk_id AS LocationID,
	placement_config.pk_id AS placement_locationsid,
	placements.pk_id AS PlacementID,
	placement_config.warehouse_id AS wh_id,
	abs(SUM((placements.quantity))) AS Qty
FROM
	placements
INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
INNER JOIN stock_batch ON placements.stock_batch_id = stock_batch.batch_id
INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
WHERE
	placement_config.warehouse_id = $wh_id
AND placements.placement_location_id = $loc_id
GROUP BY
placements.stock_batch_id,
itminfo_tab.itm_id
having Qty>0
";
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
	print json_encode(array('message'=>'Please provide wh_id,loc_id like ?wh_id=1&loc_id=2'));
}
//just for
// example: http://localhost/lmis/ws/locations.php?ID=4
?>