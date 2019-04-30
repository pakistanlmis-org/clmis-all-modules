<?php
/**
 * sync_items
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
//Getting ID
$ID =  $_REQUEST['ID'];

$query="SELECT itmrec_id,itm_id,itm_name,itm_type,itm_category, frmindex FROM itminfo_tab";

if(!empty($ID))
{
	$query=$query." WHERE itm_id in ($ID)";
}
$query=$query." ORDER BY itm_name";

$rs = mysql_query($query) or die(print mysql_error());
$rows = array();

while($r = mysql_fetch_assoc($rs))
{
	$rows[] = $r;
}

print json_encode($rows);


?>