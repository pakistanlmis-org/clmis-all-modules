<?php
/**
 * add_adjustment_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;
$strDo = "Add";
$nstkId = 0;
$ref_no = '';
$quantity = '';
//Checking adjustment_no
if (isset($_REQUEST['adjustment_no']) && !empty($_REQUEST['adjustment_no'])) {
    //Getting adjustment_no
    $trans_no = $_REQUEST['adjustment_no'];
}
//Checking adjustment_date
if (isset($_REQUEST['adjustment_date']) && !empty($_REQUEST['adjustment_date'])) {
    //Getting adjustment_date
    $adjustment_date = $_REQUEST['adjustment_date'];
    list($dd,$mm,$yy) = explode("/",$adjustment_date);
} else {
	 $adjustment_date = date('m/d/Y');
    list($dd,$mm,$yy) = explode("/",$adjustment_date);
}
//Checking ref_no
if (isset($_REQUEST['ref_no']) && !empty($_REQUEST['ref_no'])) {
    //Getting ref_no
    $ref_no = $_REQUEST['ref_no'];
}
//Checking product
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
    //Getting product
    $product = $_REQUEST['product'];
}
//Checking batch_no
if (isset($_REQUEST['batch_no']) && !empty($_REQUEST['batch_no'])) {
    //Getting batch_no
    $batch_id = $_REQUEST['batch_no'];
}
//Checking available
if (isset($_REQUEST['available']) && !empty($_REQUEST['available'])) {
    //Getting available
    $available = $_REQUEST['available'];
}
//Checking types
if (isset($_REQUEST['types']) && !empty($_REQUEST['types'])) {
    //Getting types
    $type = $_REQUEST['types'];
}
//Checking quantity
if (isset($_REQUEST['quantity']) && !empty($_REQUEST['quantity'])) {
    //Getting quantity
    $quantity = str_replace(',','',$_REQUEST['quantity']);
}
//Checking comments
if (isset($_REQUEST['comments']) && !empty($_REQUEST['comments'])) {
    //Getting comments
    $comments = $_REQUEST['comments'];
}
//Checking unit
if (isset($_REQUEST['unit']) && !empty($_REQUEST['unit'])) {
    //Getting unit
    $unit = $_REQUEST['unit'];
}else $unit = 0;
//Setting variables to the objStockMaster 
//TranDate
$objStockMaster->TranDate = $yy."-".$mm."-".$dd;
//TranTypeID
$objStockMaster->TranTypeID = $type;
//TranRef
$objStockMaster->TranRef = $ref_no;
//WHIDFrom
$objStockMaster->WHIDFrom = $_SESSION['user_warehouse'];
//WHIDTo
$objStockMaster->WHIDTo = $_SESSION['user_warehouse'];
//CreatedBy
$objStockMaster->CreatedBy = $_SESSION['user_id'];
//CreatedOn
$objStockMaster->CreatedOn = date("Y-m-d");
//ReceivedRemarks
$objStockMaster->ReceivedRemarks = $comments;
//get Fiscal Year
$fy_dates = $objFiscalYear->getFiscalYear();
//get Adj Last ID
$last_id  = $objStockMaster->getAdjLastID($fy_dates['from_date'],$fy_dates['to_date']);

if ($last_id == NULL) {
	$last_id = 0;
}
$trans_no = "A". date('ym').str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
//TranNo
$objStockMaster->TranNo = $trans_no;
//BatchID
$objStockMaster->BatchID = $batch_id;
//temp
$objStockMaster->temp = 0;
//trNo
$objStockMaster->trNo = $last_id + 1;
//LinkedTr
$objStockMaster->LinkedTr = 0;
$objStockMaster->issued_by = $_SESSION['user_warehouse'];

$fkStockID = $objStockMaster->save();

$type_nature = $objTransType->find_by_id($type);

//Add
if ($strDo == "Add") {
    $objStockDetail->fkStockID = $fkStockID;
    $objStockDetail->BatchID = $batch_id;
    $objStockDetail->Qty = $type_nature->trans_nature.$quantity;
	$objStockDetail->adjustmentType = $type;
    $objStockDetail->temp = 0;
	$objStockDetail->fkUnitID = $unit;
    $objStockDetail->IsReceived = 0;
    $objStockDetail->save();
}

$adjustedQty = $objStockBatch->adjustQtyByWh($batch_id, $_SESSION['user_warehouse']);

$objWhData->addReport($fkStockID, $type);


$_SESSION['success'] = 1;
header("location:add_adjustment.php");
exit;
?>