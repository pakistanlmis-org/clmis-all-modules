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
if (isset($_REQUEST['loc_id']) && !empty($_REQUEST['loc_id'])) {
    //get loc_id
    $locId = $_REQUEST['loc_id'];
}
//check item_id
if (isset($_REQUEST['item_id']) && !empty($_REQUEST['item_id'])) {
    //get item_id
    $item_id = $_REQUEST['item_id'];
}
//check transfer_qty
if (isset($_REQUEST['transfer_qty']) && !empty($_REQUEST['transfer_qty'])) {
    //get  transfer_qty
    $quantity = $_REQUEST['transfer_qty'];
}
//check qty_carton
if (isset($_REQUEST['qty_carton']) && !empty($_REQUEST['qty_carton'])) {
    //get qty_carton
    $carton_qty = $_REQUEST['qty_carton'];
}
//check transfer_to
if (isset($_REQUEST['transfer_to']) && !empty($_REQUEST['transfer_to'])) {
    //get transfer_to
    $transfer_to = $_REQUEST['transfer_to'];
}
//check stock_detail_id
if (isset($_REQUEST['stock_detail_id']) && !empty($_REQUEST['stock_detail_id'])) {
    //get stock_detail_id
    $stock_detail = $_REQUEST['stock_detail_id'];
}
//check batch_id
if (isset($_REQUEST['batch_id']) && !empty($_REQUEST['batch_id'])) {
    //get batch_id
    $batch_id = $_REQUEST['batch_id'];
}

$placement_transaction = '90';
$placement_transaction_to = '89';
$created_date = date('Y-m-d H:i:s');
$created_by = $_SESSION['user_id'];
$is_placed = "-1";
$quantityActual = $quantity;
$transferFromQuery = "insert into placements set placement_location_id=" . $locId . ",quantity='-" . $quantityActual . "',is_placed='" . $is_placed . "',stock_batch_id=" . $batch_id . ",stock_detail_id=" . $stock_detail . ", placement_transaction_type_id=" . $placement_transaction . ",created_date='" . $created_date . "',created_by=" . $created_by . "";
$transferRes = mysql_query($transferFromQuery) or die(mysql_eror());
if ($transferRes) {
    $transferFromQuery = "insert into placements set placement_location_id=" . $transfer_to . ",quantity='" . $quantityActual . "',is_placed='" . $is_placed . "',stock_batch_id=" . $batch_id . ",stock_detail_id=" . $stock_detail . ", placement_transaction_type_id=" . $placement_transaction . ",created_date='" . $created_date . "',created_by=" . $created_by . "";
    $transferToRes = mysql_query($transferFromQuery) or die("Transfer to");
}
$var = $_POST['hiddFld'];
$_SESSION['success'] = 1;
header("location:stock_location.php?loc_id=" . $locId . '&' . $var);
exit;
