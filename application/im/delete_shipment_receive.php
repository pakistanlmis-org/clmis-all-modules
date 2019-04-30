<?php
/**
 * delete_receive
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../includes/classes/AllClasses.php");

if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    $id = $_REQUEST['id'];

    
    $objStockDetail->deleteReceive($id);

    if (!empty($_REQUEST['p']) && $_REQUEST['p'] == 'stock') {
		$_SESSION['success'] = 2;
        redirect("stock_receive.php");
        exit;
    }
	$_SESSION['success'] = 2;
    redirect("receive_shipment.php?shipment_id=".$_REQUEST['shipment_id']);
    exit;
}

//Delete records after stock is issued
if(isset($_POST['detailId']) && !empty($_POST['detailId'])){
	$detailId = $_POST['detailId'];
	$batchId = $_POST['batchId'];
	// Delete Issue Entry
	$objStockDetail->deleteReceive($detailId);
}