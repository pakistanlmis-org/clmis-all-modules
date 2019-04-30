<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * delete_issue
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../includes/classes/AllClasses.php");

// Delete Temp record
if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
    $id = $_REQUEST['id'];

    $objStockDetail->deleteIssue($id);

    if (!empty($_REQUEST['p']) && $_REQUEST['p'] == 'stock') {
		$_SESSION['success'] = 2;
        redirect("stock_issue.php");
        exit;
    }
	$_SESSION['success'] = 2;
    redirect("new_issue.php");
    exit;
}

//Delete records after stock is issued
if(isset($_POST['detailId']) && !empty($_POST['detailId'])){
	$detailId = $_POST['detailId'];
	$batchId = $_POST['batchId'];
	// Delete Issue Entry
	$objStockDetail->deleteIssue($detailId);
}