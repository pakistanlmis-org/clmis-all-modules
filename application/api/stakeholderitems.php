<?php

/**
 * stakeholderitems
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

$ID =  $_REQUEST['ID'];

//for stakeholderitems
$query="SELECT stk_id,stkid,stk_item FROM stakeholder_item";

if(!empty($ID))
{
	$query=$query." WHERE stkid ='$ID'";
}

$rs = mysql_query($query) or die(print mysql_error());
$rows = array();
while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}
print json_encode($rows);

//example: http://localhost/lmis/ws/stakeholderitems.php?ID=9
?>