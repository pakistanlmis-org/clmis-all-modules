<?php
//echo '<pre>';print_r($_REQUEST);exit;
require_once("../includes/classes/AllClasses.php");
$nstkId = 0;
$whid_to='';
$adjustment = false;

//$stock_id_main = $_REQUEST['stock_id'];
//$stock_ids = $_REQUEST['stockid'];
//$issue_no = $_REQUEST['issue_no'];

//echo '<pre>';
//print_r($stock_id_main);
//print_r($stock_ids);
//print_r($issue_no);
//exit;

if (!empty($stock_ids)) {
    $arr_types = $objTransType->get_all();
    $array_types = array();
    foreach ($arr_types as $arrtype) {
        $array_types[$arrtype->trans_id] = $arrtype->trans_nature;
    }
    if (!empty($stock_id_main)) {
        $stock_id_main = $stock_id_main;
        $type_id = 1;
        $stockDetail = $objStockDetail->find_by_stock_id($stock_id_main);
        if (isset($issue_no) && !empty($issue_no)) {
            $issue_no = $issue_no;
            $rec_ref = $issue_no;
        }
        if (mysql_num_rows($stockDetail) > 0) {
            $data = mysql_fetch_object($stockDetail);
            $issued_by = $data->issued_by;
            $objStockMaster->TranTypeID = $type_id;
            $objStockMaster->TranDate = date('Y-m-d');
            $objStockMaster->TranRef = $rec_ref;
            $objStockMaster->WHIDFrom = $data->WHIDFrom;
            $objStockMaster->issued_by = $issued_by;
            $objStockMaster->WHIDTo = $data->WHIDTo;
            $whid_to = $data->WHIDTo;
            $objStockMaster->CreatedBy = $_SESSION['user_id'];
            $objStockMaster->CreatedOn = date("Y-m-d");
            $objStockMaster->ReceivedRemarks = $remarks;
            $objStockMaster->temp = 0;
            $objStockMaster->LinkedTr = 0;
            $fy_dates = $objFiscalYear->getFiscalYear();
            $last_id = $objStockMaster->getLastID($fy_dates['from_date'], $fy_dates['to_date'], 2);

            if ($last_id == NULL) {
                $last_id = 0;
            }
            //transaction number
            $trans_no = "R" .  date('ym').str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
            $objStockMaster->TranNo = $trans_no;
            $objStockMaster->trNo = ($last_id + 1);
            $fkStockID = $objStockMaster->save();
        }
    }
    //echo 'A';
    if (!empty($stock_ids)) {
        $count = count($stock_ids);
        //echo 'b';
        foreach ($stock_ids as $index => $detail_id) {
            //echo 'c';
            $objStockDetail->StockReceived($detail_id);
            $stockBatch = $objStockDetail->GetBatchDetail($detail_id);
            $quantity = str_replace("-", "", $stockBatch->Qty);
            $product_id = $stockBatch->item_id;
            $objStockBatch->batch_no = $stockBatch->batch_no;
            $objStockBatch->batch_expiry = $stockBatch->batch_expiry;
            $objStockBatch->funding_source = $stockBatch->funding_source;
            $objStockBatch->manufacturer = $stockBatch->manufacturer;
            $objStockBatch->Qty = $quantity;
            $objStockBatch->item_id = $product_id;
            $objStockBatch->status = 'Stacked';
            $objStockBatch->unit_price = $stockBatch->unit_price;
            $objStockBatch->production_date = $stockBatch->production_date;
            $objStockBatch->wh_id = $whid_to;
            $objStockBatch->check_in_wh_id = $whid_to;
            
//            echo '<pre>';
//            print_r($stockBatch);
//            print_r($objStockBatch);
//            exit;
            $batch_id1 = $objStockBatch->save();
            $objStockDetail->fkStockID = $fkStockID;
            $objStockDetail->BatchID = $batch_id1;
            $objStockDetail->fkUnitID = $data->fkUnitID;
            $objStockDetail->Qty = $array_types[$type_id] . $quantity;
            $objStockDetail->temp = 0;
            $objStockDetail->IsReceived = 1;
            $objStockDetail->adjustmentType = $type_id;
            $objStockDetail->save();
            $objStockBatch->adjustQtyByWh($batch_id1, $whid_to);
            $objStockBatch->autoRunningLEFOBatch($product_id, $whid_to);
        } // End foreach
    }
    $objWhData->addReport($fkStockID, 1, 'wh');
        //header("location:receive_voucher.php?msg=Received successfully!");
     
} else {
    //header("location:receive_voucher.php?search=1&issue_no=$issue_no&e=1");
}
?>