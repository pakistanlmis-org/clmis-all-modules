<?php


/**
 * get-rack-information
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


//for getting rack information
$query="SELECT
rack_information.pk_id,
rack_information.rack_type,
rack_information.no_of_bins,
rack_information.bin_net_capacity,
rack_information.gross_capacity,
rack_information.capacity_unit
FROM
rack_information";

$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
print json_encode($rows);

// example: http://localhost/lmis/ws/locations.php?ID=4
?>