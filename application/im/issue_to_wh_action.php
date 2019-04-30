<?php
//echo '<pre>';print_r($_REQUEST);exit;

include("../includes/classes/AllClasses.php");

$strDo = "Add";
$nstkId = 0;
$autorun = false;
$vvmstage = '';

//check issue date
$issue_date='';
if (isset($_REQUEST['issue_date']) && !empty($_REQUEST['issue_date'])) {
//get issue date	
    $issue_date = $_REQUEST['issue_date'];
}
$issue_ref='';
if (isset($_REQUEST['issue_ref']) && !empty($_REQUEST['issue_ref'])) {
//get issue reference	
    $issue_ref = $_REQUEST['issue_ref'];
}
//check issued by
if (isset($_REQUEST['issued_by']) && !empty($_REQUEST['issued_by'])) {
//get issued by	
    $issued_by = $_REQUEST['issued_by'];
}
//check warehouse
if (isset($_REQUEST['warehouse']) && !empty($_REQUEST['warehouse'])) {
//get warehouse	
    $issue_to = $_REQUEST['warehouse'];
} else {
    header("location:" . APP_URL . "im/issue.php?warehouse=1");

    exit;
}
//check product
if (isset($_REQUEST['product']) && !empty($_REQUEST['product'])) {
//get product	
    $product = $_REQUEST['product'];
}
//check item rec
if (isset($_REQUEST['itmrec']) && !empty($_REQUEST['itmrec'])) {
//get item rec	
    $itemrec = $_REQUEST['itmrec'];
}
//check batch
$batch=array();
if (isset($_REQUEST['batch']) && !empty($_REQUEST['batch'])) {
    $batch = $_REQUEST['batch'];
}
//check qty issued
if (isset($_REQUEST['qty_issued']) && !empty($_REQUEST['qty_issued'])) {
//get qty	
    $qty = $_REQUEST['qty_issued'];
}
$comments='';
if (isset($_REQUEST['comments']) && !empty($_REQUEST['comments'])) {
    $comments = $_REQUEST['comments'];
}
$c_n = 1 ;
//qty
$qty = array_filter($qty);
//batch
$batch = array_filter($batch);
$stock_detail_ids  = array();
foreach ($qty as $key => $value) {
    $batch = explode("|", $key);
    $batchId = $batch[1];
}
//set Transaction date
$objStockMaster->TranDate = dateToDbFormat($issue_date);
//set Transaction type id
$objStockMaster->TranTypeID = 2;
//set Transaction ref
$objStockMaster->TranRef = $issue_ref;
//set issued by
$objStockMaster->issued_by = (!empty($issued_by)?$issued_by:'0');
//set from warehouse
$objStockMaster->WHIDFrom = $_SESSION['user_warehouse'];
//set to warehouse
$objStockMaster->WHIDTo = $issue_to;
//set created by
$objStockMaster->CreatedBy = $_SESSION['user_id'];
//set created on
$objStockMaster->CreatedOn = date("Y-m-d");
//current year
$current_year = date("Y");
//current month
$current_month = date("m");
//echo '<pre>';print_r($objStockMaster);exit;
if ($current_month < 7) {
    $from_date = ($current_year - 1) . "-06-30";
    $to_date = $current_year . "-07-30";
} else {
    $from_date = $current_year . "-06-30";
    $to_date = ($current_year + 1) . "-07-30";
}
//last id
$last_id = $objStockMaster->getLastID($from_date, $to_date, 2);

if ($last_id == NULL) {
    $last_id = 0;
}
//transaction number
$trans_no = "I" . date('ym') . str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
//set transaction number
$objStockMaster->TranNo = $trans_no;
$objStockMaster->trNo = ($last_id + 1);
$objStockMaster->LinkedTr = 0;
$objStockMaster->temp = 0;
//save stock master
$fkStockID = $objStockMaster->save();
//add
if ($strDo == "Add") {
    foreach ($qty as $key => $value) {
        //value
        $value = str_replace(',', '', $value);
        //batch
        $batch = explode("|", $key);
        //batch id
        $batchId = $batch[1];
        //item rec
        $itemrec[$batch[0]];
        //value array
        $valueArr[$batch[0]] = $value;
        //set fk stock id
        $objStockDetail->fkStockID = $fkStockID;
        //set batch id
        $objStockDetail->BatchID = $batchId;
        //set fk unit id
        $objStockDetail->fkUnitID = (!empty($unit)?$unit:'0');
        //set qty
        $objStockDetail->Qty = "-" . $value;
        //set is received
        $objStockDetail->IsReceived = 0;
        //set adjustment type
        $objStockDetail->adjustmentType = 2;
        //set vvm stage
        $objStockDetail->vvm_stage = (!empty($vvmstage)?$vvmstage:'0');
        $objStockDetail->temp = 0;
        
        //set comment
        if($c_n==1)
            $objStockDetail->comments = $comments;
        else
            $objStockDetail->comments = '';
            
        $result = $objStockDetail->save();
        $c_n++;
        $stock_detail_ids[] = $result;

        // Adjust Batch Quantity
        $objStockBatch->adjustQtyByWh($batchId, $_SESSION['user_warehouse']);

        if ($autorun == true) {
            $objStockBatch->autoRunningLEFOBatch($batch[0], $_SESSION['user_warehouse']);
            $objStockBatch->changeStatus($batchId, 'Finished');
        }
    }
}

$objWhData->addReport($fkStockID, 2);
$result = $objStockMaster->getWhLevelByStockID($fkStockID);
//echo '<pre>';print_r($result);


//$stock_id_main  = $fkStockID;
//$stock_ids      = $stock_detail_ids;
//$issue_no       = $trans_no;
//include("receive_voucher_func.php");

// instead of the above rcv voucher func file, use the following func
$objStockMaster->autoReceiveData($fkStockID);

if(!empty($_REQUEST['ref_page']))
    header("location:" . APP_URL . "im/".$_REQUEST['ref_page'].".php");
else
    header("location:" . APP_URL . "im/distribution_plan_district_level.php");

exit;
?>