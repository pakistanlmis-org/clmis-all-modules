<?php
echo '<pre>';print_r($_REQUEST);exit;
/*
Array
(
    [missing] => Array
        (
            [0] => 2
            [1] => 
        )

    [types] => Array
        (
            [0] => 5
        )

    [stockid] => Array
        (
            [0] => 33988
            [1] => 33989
        )

    [remarks] => remakedddd
    [rec_ref] => reci refreeenn
    [rec_date] => 17/05/2017
    [stock_id] => 9581
    [issue_no] => I17050629
    [count] => 2
)
 *  */
/**
 * new_receive_wh_action
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
$strDo = "Add";
$nstkId = 0;
//adjustment
$adjustment = false;
//check issue number
if (isset($_REQUEST['issue_no']) && !empty($_REQUEST['issue_no'])) {
    //get issue number
    $issue_no = $_REQUEST['issue_no'];
}
//check stock id
if (isset($_POST['stockid']) && !empty($_POST['stockid'])) {
    $arr_types = $objTransType->get_all();
    $array_types = array();
    foreach ($arr_types as $arrtype) {
        $array_types[$arrtype->trans_id] = $arrtype->trans_nature;
    }
//check stock id
    if (isset($_REQUEST['stock_id']) && !empty($_REQUEST['stock_id'])) {
        //get stock id
        $stock_id = $_REQUEST['stock_id'];
        
        $type_id = 1;
        //find by stock id
        $stockDetail = $objStockDetail->find_by_stock_id($stock_id);
        //check remarks
        if (isset($_REQUEST['remarks']) && !empty($_REQUEST['remarks'])) {
            //get remarks
            $remarks = $_REQUEST['remarks'];
        }
        //check issue number
        if (isset($_REQUEST['issue_no']) && !empty($_REQUEST['issue_no'])) {
            //get issue number
            $issue_no = $_REQUEST['issue_no'];
        }
        if (isset($_REQUEST['count']) && !empty($_REQUEST['count'])) {
            $count_o = $_REQUEST['count'];
        }
        //check receive date 
        if (isset($_REQUEST['rec_date']) && !empty($_REQUEST['rec_date'])) {
            //get receive date
            $rec_date = $_REQUEST['rec_date'];
        }
        //check receive reference
        if (isset($_REQUEST['rec_ref']) && !empty($_REQUEST['rec_ref'])) {
            //get receive reference
            $rec_ref = $_REQUEST['rec_ref'];
        }
        //check vvm
        if (isset($_REQUEST['vvmstage']) && !empty($_REQUEST['vvmstage'])) {
            //get vvm
            $vvmstage = $_REQUEST['vvmstage'];
        }
        //check cold chain
        if (isset($_REQUEST['cold_chain']) && !empty($_REQUEST['cold_chain'])) {
            //get cold chain
            $cold_chain = $_REQUEST['cold_chain'];
        }

        if (mysql_num_rows($stockDetail) > 0) {
            $data = mysql_fetch_object($stockDetail);
            $issued_by = $data->issued_by;
            if(empty($issued_by)){
                $issued_by = 119;
            }
            //transaction type id
            $objStockMaster->TranTypeID = $type_id;
            //transaction date
            $objStockMaster->TranDate = dateToDbFormat($rec_date);
            //transaction reference
            $objStockMaster->TranRef = $rec_ref;
            //from warehouse
            $objStockMaster->WHIDFrom = $data->WHIDFrom;
            //issued by
            $objStockMaster->issued_by = $issued_by;
            //to warehouse
            $objStockMaster->WHIDTo = $_SESSION['user_warehouse'];
            //created by
            $objStockMaster->CreatedBy = $_SESSION['user_id'];
            //created on
            $objStockMaster->CreatedOn = date("Y-m-d");
            //Received Remarks 
            $objStockMaster->ReceivedRemarks = $remarks;
            //temp
            $objStockMaster->temp = 0;
            //LinkedTr 
            $objStockMaster->LinkedTr = 0;
            //get get Fiscal Year
            $fy_dates = $objFiscalYear->getFiscalYear();
            //get Last ID
            $last_id = $objStockMaster->getLastID($fy_dates['from_date'], $fy_dates['to_date'], $type_id);

            if ($last_id == NULL) {
                $last_id = 0;
            }
            //transaction number
            $trans_no = "R" .  date('ym').str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
            //set transaction number
            $objStockMaster->TranNo = $trans_no;
            $objStockMaster->trNo = ($last_id + 1);
            $fkStockID = $objStockMaster->save();
        }
    }
    //check stock id
    if (isset($_REQUEST['stockid']) && !empty($_REQUEST['stockid'])) {
        //get stock id
        $stock_ids = $_REQUEST['stockid'];
        $count = count($stock_ids);
        
        foreach ($stock_ids as $index => $detail_id) {
            //Stock Received
            $objStockDetail->StockReceived($detail_id);
            //Get Batch Detail
            $stockBatch = $objStockDetail->GetBatchDetail($detail_id);
            //get missing
            $array_missing = $_REQUEST['missing'];
            if (isset($array_missing[$index]) && !empty($array_missing[$index])) {
                //get missing
                $missing = $_REQUEST['missing'];
                //get type
                $type = $_REQUEST['types'];
                //find by detail id
                $stockDetail = $objStockDetail->find_by_detail_id($detail_id);
                //
                if (mysql_num_rows($stockDetail) > 0) {
                    //fetch results
                    $data = mysql_fetch_object($stockDetail);
                    //transaction type
                    $objStockMaster->TranTypeID = $type[$index];
                    //transaction date
                    $objStockMaster->TranDate = dateToDbFormat($rec_date);
                    //transaction reference
                    $objStockMaster->TranRef = $rec_ref;
                    //from warehouse
                    $objStockMaster->WHIDFrom = $_SESSION['user_warehouse'];
                    //to warehouse
                    $objStockMaster->WHIDTo = $_SESSION['user_warehouse'];
                    //created by
                    $objStockMaster->CreatedBy = $_SESSION['user_id'];
                    //created on
                    $objStockMaster->CreatedOn = date("Y-m-d");
                    //received remarks
                    $objStockMaster->ReceivedRemarks = $remarks;
                    //temp
                    $objStockMaster->temp = 0;
                    //linked Tr
                    $objStockMaster->LinkedTr = $fkStockID;
                    //get fiscal year
                    $fy_dates = $objFiscalYear->getFiscalYear();
                    //get Adj Last ID
                    $last_id = $objStockMaster->getAdjLastID($fy_dates['from_date'], $fy_dates['to_date']);
                    //if last id null set it to zero
                    if ($last_id == NULL) {
                        $last_id = 0;
                    }
                    //transaction number
                    $trans_no = "A" .  date('ym').str_pad(($last_id + 1), 4, "0", STR_PAD_LEFT);
                    //transaction number
                    $objStockMaster->TranNo = $trans_no;
                    $objStockMaster->trNo = ($last_id + 1);
                    //save stock master
                    $StockID = $objStockMaster->save();
                }

                $adjustment = true;
            }
            //quantity
            $quantity = str_replace("-", "", $stockBatch->Qty);
            //product id
            $product_id = $stockBatch->item_id;
            //batch number
            $objStockBatch->batch_no = $stockBatch->batch_no;
            //batch expiry
            $objStockBatch->batch_expiry = $stockBatch->batch_expiry;
            //quantity
            $objStockBatch->Qty = $quantity;
            //item id
            $objStockBatch->item_id = $product_id;
            //status
            $objStockBatch->status = 'Stacked';
            //unit price
            $objStockBatch->unit_price = $stockBatch->unit_price;
            //production date
            $objStockBatch->production_date = $stockBatch->production_date;
            //warehouse id
            $objStockBatch->wh_id = $_SESSION['user_warehouse'];
            //save stock batch
            $batch_id1 = $objStockBatch->save();

            if ($adjustment) {
                // Detail Entry for Adjustment
                //fk stock id
                $objStockDetail->fkStockID = $StockID;
                //batch id
                $objStockDetail->BatchID = $batch_id1;
                //fk unit id
                $objStockDetail->fkUnitID = $data->fkUnitID;
                //quantity
                $objStockDetail->Qty = $array_types[$type[$index]] . $missing[$index];
                //temp
                $objStockDetail->temp = 0;
                //is received
                $objStockDetail->IsReceived = 0;
                //adjustment type
                $objStockDetail->adjustmentType = $type[$index];
                //save stock detail
                $objStockDetail->save();
            }
            //fk stock id
            $objStockDetail->fkStockID = $fkStockID;
            //batch id
            $objStockDetail->BatchID = $batch_id1;
            //fk unit id
            $objStockDetail->fkUnitID = $data->fkUnitID;
            //quantity
            $objStockDetail->Qty = $array_types[$type_id] . $quantity;
            //temp
            $objStockDetail->temp = 0;
            //is received
            $objStockDetail->IsReceived = 1;
            //adjustment type
            $objStockDetail->adjustmentType = $type_id;
            //save stock detail
            $objStockDetail->save();

            // Adjust Batch Quantity
            $objStockBatch->adjustQtyByWh($batch_id1, $_SESSION['user_warehouse']);
            //auto Running LEFO Batch
            $objStockBatch->autoRunningLEFOBatch($product_id, $_SESSION['user_warehouse']);
        } // End foreach
    }

    //Save Data in WH data table (Need modification)
    $objWhData->addReport($fkStockID, 1, 'wh');
    if ($count == $count_o) {
        header("location:new_receive_wh.php?msg=Received successfully!");
    } else {
        header("location:new_receive_wh.php?search=1&issue_no=$issue_no");
    }
} else {
    header("location:new_receive_wh.php?search=1&issue_no=$issue_no&e=1");
}
exit;
?>