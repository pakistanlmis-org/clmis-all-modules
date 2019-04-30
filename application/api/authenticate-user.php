<?php

/**
 * authenticate-user
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

//authenticate user
$query="SELECT
			tbl_warehouse.wh_id AS WHID,
			tbl_warehouse.wh_name AS WHName,
			sysuser_tab.UserID AS userId,
			sysuser_tab.sysusr_name AS userName
		FROM
			tbl_warehouse
		INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
		INNER JOIN sysuser_tab ON wh_user.sysusrrec_id = sysuser_tab.UserID
		WHERE
			sysuser_tab.auth = '$auth' ";

$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
print json_encode($rows);
// example: http://localhost/lmis/ws/locations.php?ID=4
?>