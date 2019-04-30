<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");
$nstkId = 0;
$whid_to='';
$adjustment = false;
if (isset($_REQUEST['issue_no']) && !empty($_REQUEST['issue_no'])) {
    $issue_no = $_REQUEST['issue_no'];
}
if (isset($_POST['stockid']) && !empty($_POST['stockid'])) {
    $arr_types = $objTransType->get_all();
    $array_types = array();
    foreach ($arr_types as $arrtype) {
        $array_types[$arrtype->trans_id] = $arrtype->trans_nature;
    }
    if (isset($_REQUEST['stock_id']) && !empty($_REQUEST['stock_id'])) {
        $stock_id = $_REQUEST['stock_id'];
        
        $type_id = 1;
        $stockDetail = $objStockDetail->find_by_stock_id($stock_id);
        if (isset($_REQUEST['remarks']) && !empty($_REQUEST['remarks'])) {
            $remarks = $_REQUEST['remarks'];
        }
        if (isset($_REQUEST['issue_no']) && !empty($_REQUEST['issue_no'])) {
            $issue_no = $_REQUEST['issue_no'];
        }
        if (isset($_REQUEST['count']) && !empty($_REQUEST['count'])) {
            $count_o = $_REQUEST['count'];
        }
        if (isset($_REQUEST['rec_date']) && !empty($_REQUEST['rec_date'])) {
            $rec_date = $_REQUEST['rec_date'];
        }
        if (isset($_REQUEST['rec_ref']) && !empty($_REQUEST['rec_ref'])) {
            $rec_ref = $_REQUEST['rec_ref'];
        }

        if (mysql_num_rows($stockDetail) > 0) {
            $data = mysql_fetch_object($stockDetail);
            $issued_by = $data->issued_by;
            if(empty($issued_by)){
                $issued_by = 119;
            }
            $objStockMaster->TranTypeID = $type_id;
            $objStockMaster->TranDate = dateToDbFormat($rec_date);
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
            //set transaction number
            //echo $trans_no;exit;
            $objStockMaster->TranNo = $trans_no;
            $objStockMaster->trNo = ($last_id + 1);
            $fkStockID = $objStockMaster->save();
        }
    }
    if (isset($_REQUEST['stockid']) && !empty($_REQUEST['stockid'])) {
        $stock_ids = $_REQUEST['stockid'];
        $count = count($stock_ids);
        
        foreach ($stock_ids as $index => $detail_id) {
            $objStockDetail->StockReceived($detail_id);
            $stockBatch = $objStockDetail->GetBatchDetail($detail_id);
            $array_missing = $_REQUEST['missing'];
            
            if (isset($array_missing[$index]) && !empty($array_missing[$index])) {
                $missing = $_REQUEST['missing'];
                $type = $_REQUEST['types'];
                $stockDetail = $objStockDetail->find_by_detail_id($detail_id);
                if (mysql_num_rows($stockDetail) > 0) {
                    $data = mysql_fetch_object($stockDetail);
                    $objStockMaster->TranTypeID = $type[$index];
                    $objStockMaster->TranDate = dateToDbFormat($rec_date);
                    $objStockMaster->TranRef = $rec_ref;
                    $objStockMaster->WHIDFrom = $data->WHIDFrom;
                    $objStockMaster->WHIDTo = $data->WHIDTo;
                    $objStockMaster->CreatedBy = $_SESSION['user_id'];
                    $objStockMaster->CreatedOn = date("Y-m-d");
                    $objStockMaster->ReceivedRemarks = $remarks;
                    $objStockMaster->temp = 0;
                    $objStockMaster->LinkedTr = $fkStockID;
                    $fy_dates = $objFiscalYear->getFiscalYear();
                    $last_id = $objStockMaster->getAdjLastID($fy_dates['from_date'], $fy_dates['to_date']);
                    if ($last_id == NULL) {
                        $last_id = 0;
                    }
                    $trans_no = "A" .  date('ym').str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
                    $objStockMaster->TranNo = $trans_no;
                    $objStockMaster->trNo = ($last_id + 1);
                    $StockID = $objStockMaster->save();
                }
                $adjustment = true;
            }
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
            if ($adjustment) {
                $objStockDetail->fkStockID = $StockID;
                $objStockDetail->BatchID = $batch_id1;
                $objStockDetail->fkUnitID = $data->fkUnitID;
                $objStockDetail->Qty = $array_types[$type[$index]] . $missing[$index];
                $objStockDetail->temp = 0;
                $objStockDetail->IsReceived = 0;
                $objStockDetail->adjustmentType = $type[$index];
                $objStockDetail->save();
            }
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
    if ($count == $count_o) {
        header("location:receive_voucher.php?msg=Received successfully!");
    } else {
        header("location:receive_voucher.php?search=1&issue_no=$issue_no");
    }
} else {
    header("location:receive_voucher.php?search=1&issue_no=$issue_no&e=1");
}
exit;
?>