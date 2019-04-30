<?php
/**
 * get-items
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

// Sample Call http://localhost/clmisr2/ws/get-items.php?auth=authcode

//for items
$query="SELECT DISTINCT
			itminfo_tab.itm_id as pkId,
			itminfo_tab.itm_name as description,
			itminfo_tab.itm_category
		FROM
			itminfo_tab";

$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
print json_encode($rows);