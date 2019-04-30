<?php


/**
 * get-non-ccm-locations-status
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//Including Configuration.inc.php
include("../../application/includes/classes/Configuration.inc.php");
//Including db.php
include(APP_PATH."includes/classes/db.php");
//Including auth.php
include('auth.php');
//Checking wh_id
if(isset($_GET['wh_id']))
{
//Getting wh_id
$wh_id=$_GET['wh_id'];

//Query for getting status of non ccm locations
//This query retrieves 
//location_id
//location_name
//item_name
//item_id
//pack_quantity
//quantity
$query="SELECT
			B.pk_id AS location_id,
			B.location_name,
			A.item_name,
			A.item_id,
			A.pack_quantity,
			A.quantity
		FROM
			(
				SELECT
					placements.placement_location_id AS location_id,
					placement_config.location_name,
		
				IF (
					ROUND(
						abs(Sum(placements.quantity)) / itminfo_tab.qty_carton
					) = 0,
					NULL,
					itminfo_tab.itm_name
				) AS item_name,
		
			IF (
				ROUND(
					abs(Sum(placements.quantity)) / itminfo_tab.qty_carton
				) = 0,
				NULL,
				itminfo_tab.itm_id
			) AS item_id,
			ROUND(
				abs(Sum(placements.quantity)) / itminfo_tab.qty_carton
			) AS pack_quantity,
			abs(Sum(placements.quantity)) AS quantity
		FROM
			placements
		INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
		LEFT JOIN stock_batch ON placements.stock_batch_id = stock_batch.batch_id
		LEFT JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
		WHERE
			placement_config.warehouse_id = $wh_id
		GROUP BY
			placement_config.pk_id,
			itminfo_tab.itm_id
			) A
		RIGHT JOIN (
			SELECT
				placement_config.pk_id,
				placement_config.location_name
			FROM
				placement_config
			WHERE
				placement_config.warehouse_id = $wh_id
		) B ON A.location_name = B.location_name";
//Query result
$rs = mysql_query($query) or die(print mysql_error());
//for result
$rows = array();
//Populate array
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
//Encode in json
print json_encode($rows);
}
else{
	print json_encode('Please provide wh_id like ?wh_id=1');
}
// example: http://localhost/lmis/ws/locations.php?ID=4
?>