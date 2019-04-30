<?php

//echo '<pre>';
//print_r($_REQUEST);
//exit;
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

$is_done = 'No';

if (!empty($_POST['detailId'])) {
    
    //first fetch the records of received voucher against this issuance voucher
        $qry = "SELECT
                    tbl_stock_master.PkStockID,
                    tbl_stock_master.TranNo,
                    tbl_stock_master.TranRef,
                    tbl_stock_detail.PkDetailID,
                    tbl_stock_detail.BatchID,
                    tbl_stock_detail.IsReceived,
                    (SELECT
                                            (sd.PkDetailID)
                                            FROM
                                            tbl_stock_master AS sm
                                            INNER JOIN tbl_stock_detail AS sd ON sm.PkStockID = sd.fkStockID
                                            INNER JOIN stock_batch AS sb ON sd.BatchID = sb.batch_id
                                            WHERE
                                            sm.TranTypeID = 1 AND
                                            sm.WHIDFrom = tbl_stock_master.WHIDFrom AND
                                            sm.WHIDTo = tbl_stock_master.WHIDTo AND
                                            sb.item_id = stock_batch.item_id
                                            and sm.TranRef = tbl_stock_master.TranNo AND
                                            sb.batch_no = stock_batch.batch_no AND
                                            IFNULL(sb.manufacturer, 0) =  IFNULL(stock_batch.manufacturer, 0)) as rcv_detail_id,
                    (SELECT
                                            (sd.BatchID)
                                            FROM
                                            tbl_stock_master AS sm
                                            INNER JOIN tbl_stock_detail AS sd ON sm.PkStockID = sd.fkStockID
                                            INNER JOIN stock_batch AS sb ON sd.BatchID = sb.batch_id
                                            WHERE
                                            sm.TranTypeID = 1 AND
                                            sm.WHIDFrom = tbl_stock_master.WHIDFrom AND
                                            sm.WHIDTo = tbl_stock_master.WHIDTo AND
                                            sb.item_id = stock_batch.item_id
                                            and sm.TranRef = tbl_stock_master.TranNo AND
                                            sb.batch_no = stock_batch.batch_no AND
                                            IFNULL(sb.manufacturer, 0) =  IFNULL(stock_batch.manufacturer, 0)) as rcv_batch_id
                    FROM
                            tbl_stock_master
                    INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                    INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                    WHERE
                        tbl_stock_detail.PkDetailID = ".$_POST['detailId']."
                        and tbl_stock_detail.BatchID = ".$_POST['batchId']."


                    ";
        $qryRes = mysql_query($qry);
        $row = mysql_fetch_array($qryRes);
        $detail_id_of_received_voucher  = $row['rcv_detail_id'];
        $batch_id_of_received_voucher   = $row['rcv_batch_id'];
    
        $row_imploded = json_encode($row);
//    echo 'Deleting issue voucher : '.$row['TranNo'].', Issued DetailID : '.$_POST['detailId'].',And its Receiving Detail ID:'.$detail_id_of_received_voucher;
//    echo ',and Batch ID of receiver : '.$batch_id_of_received_voucher;
//    exit;
        
    //Delete records of issued voucher
    $detailId = $_POST['detailId'];
    $batchId = $_POST['batchId'];
    
    //Delete records of received voucher
    if($row['IsReceived']==0 || $row['IsReceived']<1)
    {
       $objStockDetail->deleteIssue($detailId);
       $is_done = 'Only Issue Voucher Deleted';
    }
    elseif (!empty($detail_id_of_received_voucher)) {
        $objStockDetail->deleteIssue($detailId);
        $objStockDetail->deleteReceive($detail_id_of_received_voucher);
        $is_done = 'Both Deleted';
    }
}


$msg = "Issue AND Receive Vouchers deleted at district level.";
$msg .= "\r\n User name :      ".$_SESSION['user_name'];
$msg .= "\r\n District ID :      ".$_SESSION['user_district'];
$msg .= "\r\nSERVER :      ".$_SERVER['SERVER_NAME'];
$msg .= "\r\nIssue detail ID :      ".$detailId;
$msg .= "\r\nReceive detail ID :    ".$detail_id_of_received_voucher;
$msg .= "\r\nDone Flag :    ".$is_done;
$msg .= "\r\n\nParameters :    ".json_encode($_REQUEST);;
$msg .= "\r\n\nQuery Data :    ".$row_imploded;
 
mail("muhahmed@ghsc-psm.org", "Issue AND Receive Vouchers deleted", $msg);

echo 'Voucher Deleted';
//redirect("stock_issue.php");
//    exit;