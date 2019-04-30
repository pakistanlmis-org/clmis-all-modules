<?php
ini_set('max_execution_time', 0);
include_once("../includes/classes/AllClasses.php");
//include_once(PUBLIC_PATH . "html/header.php");
 $date           = (!empty($_REQUEST['date'])?$_REQUEST['date']:$d_1);
$date = date('Y-m-01',strtotime($date));
$stakeholder = mysql_real_escape_string($_REQUEST['stakeholder']);
$selProv = mysql_real_escape_string($_REQUEST['prov_sel']);
$districtId = mysql_real_escape_string($_REQUEST['dist_id']);
$itm_id = mysql_real_escape_string($_REQUEST['product']);


include "stock_optimization_email_func.php";

$a=generate_stock_table_for_email($date,$districtId,$stakeholder,$itm_id,$selProv);
echo $a;