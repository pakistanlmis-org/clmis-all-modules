<?php

/**
 * heartbeat2
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

include("../../application/includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include('auth.php');

$ID =  $_REQUEST['ID'];

 function getProdName($ID){
	$query = "SELECT itm_name FROM itminfo_tab Where itm_id=".$ID;
	$rs = mysql_query($query) or die(mysql_error());
	return $rs;

}

$result=getProdName($ID);
$row = mysql_fetch_array($result);
echo $row[0];


?>