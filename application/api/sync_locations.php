<?php
/**
 * sync_locations
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

//including required files
include("../../application/includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");

$ID =  $_REQUEST['ID'];

$query="Select PkLocID, LocName,ParentID,LocType,LocLvl  From tbl_locations";

if(!empty($ID))
{
	$query=$query." WHERE PkLocID ='$ID'";
}

$query .= " ORDER BY LocName";
$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
print json_encode($rows);

// example: http://localhost/lmis/ws/locations.php?ID=4
?>