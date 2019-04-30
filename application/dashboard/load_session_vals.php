<?php
session_start();

if(!empty($_REQUEST['what']) && $_REQUEST['what']=='summary_vals'){
	$ret = $_SESSION['dashboard_vals'];
}
//print_r($ret);
header('Content-Type: application/json');
$ret = json_encode($ret);
echo $ret;
?>