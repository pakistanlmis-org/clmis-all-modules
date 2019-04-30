<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;

$return=array();
$strDo = "Add";
$ref_no = '';
$quantity = '';
if (isset($_REQUEST['adjustment_no']) && !empty($_REQUEST['adjustment_no'])) {
    $trans_no = $_REQUEST['adjustment_no'];
}

if (isset($_REQUEST['ref_no']) && !empty($_REQUEST['ref_no'])) {
    $ref_no = $_REQUEST['ref_no'];
}
else $ref_no ='';
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
    $product = $_REQUEST['product'];
}


if (isset($_REQUEST['types']) && !empty($_REQUEST['types'])) {
    $type = $_REQUEST['types'];
}
if (isset($_REQUEST['dtl']) && !empty($_REQUEST['dtl'])) {
    $dtl = $_REQUEST['dtl'];
}else if ($_REQUEST['dtl']== 0 )
    { $dtl =0; }
else {
    $dtl =2;
}

if (isset($_REQUEST['quantity']) && !empty($_REQUEST['quantity'])) {
    $quantity = str_replace(',','',$_REQUEST['quantity']);
}
if (isset($_REQUEST['comments']) && !empty($_REQUEST['comments'])) {
    $comments = $_REQUEST['comments'];
}else $comments='';
if (isset($_REQUEST['unit']) && !empty($_REQUEST['unit'])) {
    $unit = $_REQUEST['unit'];
}else $unit = 0;

$manufacturer = $_REQUEST['manufacturer'];
$batch_name = $_REQUEST['batch'];

//first check if the batch_no already exists ??
$qry = "Select count(*) as c
        from stock_batch
            WHERE  stock_batch.batch_no = '".$batch_name."' AND "
        . " stock_batch.wh_id = '".$_SESSION['user_warehouse']."' AND
            stock_batch.manufacturer = $manufacturer";
//echo $qry;exit;
$res =  mysql_query($qry);
$row = mysql_fetch_assoc($res);
$c_batch = $row['c'];

if(!empty($c_batch) && $c_batch>0){

    $return['saved']='no';
    $return['msg']='Batch named "'.$batch_name.'" of the this manufacturer Already exists in your warehouse.';

echo json_encode($return);
exit;
}


//adding the New Batch
$expiry_date = '';
if(!empty($_REQUEST['expiry']) && $_REQUEST['expiry']!='undefined'){
    //list($d, $m, $y) = explode('-', $_REQUEST['expiry']);
    //$expiry_date = $y.'-'.$m.'-'.$d;
    $expiry_date=$_REQUEST['expiry'];
}
$receive_from = $_REQUEST['receive_from'];
$product = $_REQUEST['product'];
    $qry = "INSERT INTO stock_batch
            SET
                    stock_batch.batch_no = '".$batch_name."',
                    stock_batch.batch_expiry = '".$expiry_date."',
                    stock_batch.item_id = '".$product."',
                    stock_batch.`status` = 'Finished',
                    stock_batch.wh_id = '".$_SESSION['user_warehouse']."',
                    stock_batch.funding_source = '".$receive_from."', ";
    $qry .= "       stock_batch.dtl = '".$dtl."', ";
    $qry .= "       stock_batch.manufacturer = '".$manufacturer."' ";
    //query result
    //echo $qry;exit;
    mysql_query($qry);
    $batch_id =  mysql_insert_id();

//End adding New Batch

$objStockMaster->TranDate = date('Y-m-d');
$objStockMaster->TranTypeID = $type;
$objStockMaster->TranRef = $ref_no;
$objStockMaster->WHIDFrom = $_SESSION['user_warehouse'];
$objStockMaster->WHIDTo = $_SESSION['user_warehouse'];
$objStockMaster->CreatedBy = $_SESSION['user_id'];
$objStockMaster->CreatedOn = date("Y-m-d");
$objStockMaster->ReceivedRemarks = $comments;
$fy_dates = $objFiscalYear->getFiscalYear();

$last_id  = $objStockMaster->getAdjLastID(date('Y-m-01'),date('Y-m-t'));

if ($last_id == NULL) {
	$last_id = 0;
}
$trans_no = "A". date('ym').str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
$objStockMaster->TranNo = $trans_no;
$objStockMaster->BatchID = $batch_id;
$objStockMaster->temp = 0;
$objStockMaster->trNo = $last_id + 1;
$objStockMaster->LinkedTr = 0;
$objStockMaster->issued_by = $_SESSION['user_warehouse'];

$fkStockID = $objStockMaster->save();
$type_nature = $objTransType->find_by_id($type);

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



$return = $_REQUEST;
$return['batch_id']         = $batch_id;
$return['adjusted_qty']     = $adjustedQty;
$return['stock_master_id']  = $fkStockID;
$return['saved']='ok';

echo json_encode($return);
exit;