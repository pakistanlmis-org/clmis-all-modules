<?php

/**
 * transfer_stock_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses file
include("../includes/classes/AllClasses.php");

$strDo = "Add";
$nstkId = 0;
$remarks = '';
//check loc_id
if (isset($_REQUEST['hidden_loc_id']) && !empty($_REQUEST['hidden_loc_id'])) {
    //get loc_id
    $locId = $_REQUEST['hidden_loc_id'];
}


//check batch_id
if (isset($_REQUEST['volume_used']) && !empty($_REQUEST['volume_used'])) {
    //get batch_id
    $volume_used = $_REQUEST['volume_used'];
}


$created_date = date('Y-m-d H:i:s');
$created_by = $_SESSION['user_id'];

if ($volume_used) {
    $transferFromQuery = "UPDATE placement_config set placement_config.volume_used = '$volume_used' where placement_config.pk_id = '$locId' ";
    $transferToRes = mysql_query($transferFromQuery) or die("Transfer to");
}
$var = $_REQUEST['hiddFld'];
$_SESSION['success'] = 1;
header("location:stock_location.php?loc_id=" . $locId . '&' . $var);
exit;
