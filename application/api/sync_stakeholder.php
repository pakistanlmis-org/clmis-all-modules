<?php

/**
 * sync_stakeholder
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
include(APP_PATH . "includes/classes/db.php");


//for sync_stakeholder
$ID = $_REQUEST['ID'];
$query = "SELECT
			stkid,
			stkname,
			lvl,
			report_title1,
			report_title2,
			report_title3,
			report_logo,
			stkcode,
			stkorder
		FROM
			stakeholder";
if (!empty($ID)) {
    $query = $query . " WHERE stkid ='$ID' ";
}
$query .= " ORDER BY stkname";
//where lvl =4 
$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
while ($r = mysql_fetch_assoc($rs)) {
    $rows[] = $r;
}
//Encode in json
print json_encode($rows);
?>