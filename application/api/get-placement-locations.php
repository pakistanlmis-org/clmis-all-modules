<?php


/**
 * get-placement-locations
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

//for getting placement locations
$query="SELECT
placement_config.pk_id,
placement_config.location_name as location_barcode,
'100' as location_type,
placement_config.pk_id as location_id
FROM
placement_config
";

$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
print json_encode($rows);
?>