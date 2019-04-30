<?php


/**
 * get-non-ccm-locations
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

$wh_id = $_REQUEST['wh_id'];
$query="SELECT
	placement_config.pk_id,
	placement_config.location_name,
	placement_config.rack_information_id
FROM
	placement_config
WHERE
	placement_config.warehouse_id = $wh_id";

$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
print json_encode($rows);

// example: http://localhost/lmis/ws/locations.php?ID=4
?>